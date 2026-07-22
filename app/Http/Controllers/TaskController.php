<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\TaskNotification;           // ✅ যোগ করুন
use Illuminate\Support\Facades\Notification;    
class TaskController extends Controller
{
    /*
    |--------------------------------------------------
    | LIST with filters
    |--------------------------------------------------
    */
    public function index(Request $request)
    {
        $query = Task::query()->with('users'); // ✅ assigned users eager load

        // Hide original tasks that have been carried forward
        $query->whereNotIn('id', function ($sub) {
            $sub->select('carried_from_id')
                ->from('tasks')
                ->where('is_carried_forward', true)
                ->whereNotNull('carried_from_id');
        });

        // ✅ My Task filter — logged-in user er assigned task
        if ($request->boolean('my_task')) {
            $query->whereHas('users', function ($q) {
                $q->where('users.id', auth()->id());
            });
        }

        // Date range filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('issue_date', [
                $request->from_date,
                $request->to_date,
            ]);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Month filter
        if ($request->filled('month')) {
            $query->whereYear('issue_date', substr($request->month, 0, 4))
                  ->whereMonth('issue_date', substr($request->month, 5, 2));
        }

        $tasks = $query->orderByRaw("FIELD(status,'pending','approved','completed','cancelled')")
                       ->orderBy('end_date')
                       ->paginate(20)
                       ->withQueryString();

        // Summary (also exclude hidden original tasks)
        $summary = Task::query()
            ->whereNotIn('id', function ($sub) {
                $sub->select('carried_from_id')
                    ->from('tasks')
                    ->where('is_carried_forward', true)
                    ->whereNotNull('carried_from_id');
            })
            ->when($request->boolean('my_task'), fn($q) =>
                $q->whereHas('users', fn($q2) => $q2->where('users.id', auth()->id()))
            )
            ->when($request->filled('from_date') && $request->filled('to_date'), fn($q) =>
                $q->whereBetween('issue_date', [$request->from_date, $request->to_date])
            )
            ->when($request->filled('month'), fn($q) =>
                $q->whereYear('issue_date', substr($request->month, 0, 4))
                  ->whereMonth('issue_date', substr($request->month, 5, 2))
            )
            ->selectRaw("
                COUNT(*) as total,
                SUM(status = 'pending')   as pending,
                SUM(status = 'approved')  as approved,
                SUM(status = 'completed') as completed,
                SUM(status = 'cancelled') as cancelled,
                SUM(budget)               as total_budget,
                SUM(actual_cost)          as total_spent
            ")
            ->first();

        // ✅ Approve modal-এর জন্য active user list
        $users = User::orderBy('name')->get();

        return view('pages.tasks.index', compact('tasks', 'summary', 'users'));
    }

    /*
    |--------------------------------------------------
    | CREATE
    |--------------------------------------------------
    */
    public function create()
    {
        return view('pages.tasks.create');
    }

    /*
    |--------------------------------------------------
    | STORE
    |--------------------------------------------------
    */
    public function store(Request $request)
{
    $request->validate([
        'title'      => 'required|string|max:255',
        'issue_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:issue_date',
        'budget'     => 'required|numeric|min:0',
        'priority'   => 'required|in:low,medium,high',
    ]);

    $task = Task::create([              // ✅ $task = যোগ করা হলো
        'title'       => $request->title,
        'description' => $request->description,
        'issue_date'  => $request->issue_date,
        'end_date'    => $request->end_date,
        'budget'      => $request->budget,
        'priority'    => $request->priority,
        'remarks'     => $request->remarks,
        'status'      => 'pending',
        'created_by'  => auth()->id(),
    ]);

    Notification::send(User::administrators()->get(), new TaskNotification($task, 'created'));
    return redirect()
        ->route('tasks.index')
        ->with('success', 'Task তৈরি হয়েছে।');
}

    /*
    |--------------------------------------------------
    | EDIT
    |--------------------------------------------------
    */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        return view('pages.tasks.edit', compact('task'));
    }

    /*
    |--------------------------------------------------
    | UPDATE
    |--------------------------------------------------
    */
   public function update(Request $request, $id)
{
    $request->validate([
        'title'      => 'required|string|max:255',
        'issue_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:issue_date',
        'budget'     => 'required|numeric|min:0',
        'priority'   => 'required|in:low,medium,high',
    ]);

    $task = Task::findOrFail($id);      // ✅ আগে ভেরিয়েবলে ধরুন
    $task->update([                     // ✅ তারপর update করুন
        'title'       => $request->title,
        'description' => $request->description,
        'issue_date'  => $request->issue_date,
        'end_date'    => $request->end_date,
        'budget'      => $request->budget,
        'priority'    => $request->priority,
        'remarks'     => $request->remarks,
        'updated_by' => auth()->id(),
    ]);

    Notification::send(User::administrators()->get(), new TaskNotification($task, 'updated'));
    return redirect()
        ->route('tasks.index')
        ->with('success', 'Task আপডেট হয়েছে।');
}

    /*
    |--------------------------------------------------
    | APPROVE — user(s) assign করে status = approved
    |--------------------------------------------------
    */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $task = Task::findOrFail($id);

        // ✅ নির্বাচিত user গুলো task-এ assign (sync = আগেরগুলো replace হবে)
        $task->users()->sync($request->user_ids);

        $task->update([
    'status'      => 'approved',
    'approved_by' => auth()->id(),
]);

Notification::send(
    User::administrators()->get(),
    new TaskNotification($task, 'approved')
);

return back()->with('success', 'Task approve করা হয়েছে এবং user assign করা হয়েছে।');
    }

    /*
    |--------------------------------------------------
    | CANCEL
    |--------------------------------------------------
    */
    public function cancel($id)
    {
        Task::findOrFail($id)->update(['status' => 'cancelled']);

        return back()->with('success', 'Task cancel করা হয়েছে।');
    }

    /*
    |--------------------------------------------------
    | MARK AS PENDING
    |--------------------------------------------------
    */
    public function markPending($id)
    {
        Task::findOrFail($id)->update(['status' => 'pending']);

        return back()->with('success', 'Task pending এ ফিরিয়ে আনা হয়েছে।');
    }

    /*
    |--------------------------------------------------
    | COMPLETE
    |--------------------------------------------------
    */
    public function complete(Request $request, $id)
{
    $request->validate([
        'actual_cost' => 'required|numeric|min:0',
    ]);

    $task = Task::findOrFail($id);      // ✅ আগে ভেরিয়েবলে ধরুন
    $task->update([                     // ✅ তারপর update করুন
        'status'          => 'completed',
        'actual_cost'     => $request->actual_cost,
        'completion_date' => now()->toDateString(),
        'remarks'         => $request->remarks,
        'completed_by'    => auth()->id(),
    ]);

    Notification::send(User::administrators()->get(), new TaskNotification($task, 'completed'));
    return back()->with('success', 'Task সম্পন্ন হয়েছে।');
}

    /*
    |--------------------------------------------------
    | CARRY FORWARD — Single Task
    |--------------------------------------------------
    */
    public function carryForwardSingle(Request $request)
    {
        $request->validate([
            'task_id'        => 'required|exists:tasks,id',
            'new_start_date' => 'required|date',
            'new_end_date'   => 'required|date|after_or_equal:new_start_date',
        ]);

        $task = Task::findOrFail($request->task_id);

        $alreadyCarried = Task::where('carried_from_id', $task->id)->exists();
        if ($alreadyCarried) {
            return back()->with('error', 'এই task ইতিমধ্যে carry forward হয়েছে।');
        }

        Task::create([
            'title'              => $task->title,
            'description'        => $task->description,
            'issue_date'         => $request->new_start_date,
            'end_date'           => $request->new_end_date,
            'budget'             => $task->budget,
            'priority'           => $task->priority,
            'remarks'            => $task->remarks,
            'status'             => 'pending',
            'is_carried_forward' => true,
            'carried_from_id'    => $task->id,
            'created_by'         => auth()->id(),
        ]);

        return back()->with('success', "{$task->title} carry forward হয়েছে।");
    }

    /*
    |--------------------------------------------------
    | CARRY FORWARD — Bulk (by month)
    |--------------------------------------------------
    */
    public function carryForward(Request $request)
    {
        $request->validate([
            'month'          => 'required|date_format:Y-m',
            'new_start_date' => 'required|date',
            'new_end_date'   => 'required|date|after_or_equal:new_start_date',
        ]);

        $year  = substr($request->month, 0, 4);
        $month = substr($request->month, 5, 2);

        $pendingTasks = Task::whereIn('status', ['pending', 'approved'])
            ->whereYear('end_date', $year)
            ->whereMonth('end_date', $month)
            ->whereNotIn('id', function ($sub) {
                $sub->select('carried_from_id')
                    ->from('tasks')
                    ->where('is_carried_forward', true)
                    ->whereNotNull('carried_from_id');
            })
            ->get();

        if ($pendingTasks->isEmpty()) {
            return back()->with('error', 'ওই মাসে কোনো pending task নেই অথবা সব tasks ইতিমধ্যে carry forward হয়েছে।');
        }

        $count = 0;
        foreach ($pendingTasks as $task) {
            Task::create([
                'title'              => $task->title,
                'description'        => $task->description,
                'issue_date'         => $request->new_start_date,
                'end_date'           => $request->new_end_date,
                'budget'             => $task->budget,
                'priority'           => $task->priority,
                'remarks'            => $task->remarks,
                'status'             => 'pending',
                'is_carried_forward' => true,
                'carried_from_id'    => $task->id,
                'created_by'         => auth()->id(),
            ]);

            $count++;
        }

        return back()->with('success', "{$count}টি pending task carry forward হয়েছে।");
    }

    /*
    |--------------------------------------------------
    | DELETE
    |--------------------------------------------------
    */
    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return back()->with('success', 'Task মুছা হয়েছে।');
    }

    public function taskNotifications()
{
    $notifications = auth()->user()
        ->unreadNotifications()
        ->where('type', \App\Notifications\TaskNotification::class)
        ->latest()
        ->take(10)
        ->get();

    return response()->json($notifications);
}

public function markNotificationRead($id)
{
    $notification = auth()->user()->notifications()->findOrFail($id);
    $notification->markAsRead();

    return response()->json(['success' => true]);
}
}