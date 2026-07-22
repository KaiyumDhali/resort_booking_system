<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AddUserModal extends Component
{
    use WithFileUploads;

    public $name;
public $email;
public $role;
public $avatar;
public $saved_avatar;
public $password;        // ✅ add
public $edit_mode = false;

protected $rules = [
    'name'     => 'required|string',
    'email'    => 'required|email',
    'role'     => 'required|string',
    'avatar'   => 'nullable|sometimes|image|max:1024',
    'password' => 'nullable|string|min:6',  // ✅ add - edit mode এ optional
];

    protected $listeners = [
        'delete_user' => 'deleteUser',
        'update_user' => 'updateUser',
    ];

    public function render()
    {
        $roles = Role::all();

        $roles_description = [
            'administrator' => 'Best for business owners and company administrators',
            'developer' => 'Best for developers or people primarily using the API',
            'analyst' => 'Best for people who need full access to analytics data, but don\'t need to update business settings',
            'support' => 'Best for employees who regularly refund payments and respond to disputes',
            'trial' => 'Best for people who need to preview content data, but don\'t need to make any updates',
        ];

        foreach ($roles as $i => $role) {
            $roles[$i]->description = $roles_description[$role->name] ?? '';
        }

        return view('livewire.user.add-user-modal', compact('roles'));
    }

    public function submit()
    {
    if ($this->edit_mode) {
    $this->validate([
        'name'   => 'required|string',
        'email'  => 'required|email',
        'role'   => 'required|string',
        'avatar' => 'nullable|image|max:1024',
    ]);
} else {
    $this->validate([
        'name'     => 'required|string',
        'email'    => 'required|email',
        'role'     => 'required|string',
        'avatar'   => 'nullable|image|max:1024',
        'password' => 'nullable|string|min:6',
    ]);
}

        DB::transaction(function () {
            // Prepare the data for creating a new user
            $data = [
                'name' => $this->name,
            ];

            if ($this->avatar) {
                $data['profile_photo_path'] = $this->avatar->store('avatars', 'public');
            } else {
                $data['profile_photo_path'] = null;
            }

          if (!$this->edit_mode) {
    // ✅ password দিলে সেটা, না দিলে email দিয়ে default
    $data['password'] = Hash::make($this->password ?: $this->email);
}

            // Create a new user record in the database
            $user = User::updateOrCreate([
                'email' => $this->email,
            ], $data);

            if ($this->edit_mode) {
                // Assign selected role for user
                $user->syncRoles($this->role);

                // Emit a success event with a message
                $this->emit('success', __('User updated'));
            } else {
                // Assign selected role for user
                $user->assignRole($this->role);

                // Send a password reset link to the user's email
                // Password::sendResetLink($user->only('email'));

                // Emit a success event with a message
                $this->emit('success', __('New user created'));
            }
        });

        // Reset the form fields after successful submission
        $this->reset();
    }

    public function deleteUser($id)
    {
        // Prevent deletion of current user
        if ($id == Auth::id()) {
            $this->emit('error', 'User cannot be deleted');
            return;
        }

        // Delete the user record with the specified ID
        User::destroy($id);

        // Emit a success event with a message
        $this->emit('success', 'User successfully deleted');
    }

    public function updateUser($id)
    {
        $this->edit_mode = true;

        $user = User::find($id);

        $this->saved_avatar = $user->profile_photo_url;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->roles?->first()->name ?? '';
    }

    public function hydrate()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
}
