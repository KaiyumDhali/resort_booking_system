@extends('layouts.admin')

@section('content')

<style>
    .checkbox__marker {
        border: 1px solid #000;
    }
</style>

<main class="page-content">
    <div class="container">
        <div class="page-header">
            <h4>Role: {{ $role->name }}</h4>
        </div>

        <div class="table-wrapper">
            <div class="table-wrapper__content table-products table-collapse scrollbar-thin scrollbar-visible" data-simplebar>

                <form action="{{ route('addPermissionToRole', $role->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @error('permission')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror

                    @php
                    // Array to store the permissions grouped by model
                    $groupedPermissions = [];

                    foreach ($permissions as $permission) {
                    // Split permission name by space (assuming format "action model")
                    $parts = explode(' ', $permission->name);

                    if (count($parts) == 2) {
                    // First part is the action (create, update, view, delete)
                    $action = $parts[0];
                    // Second part is the model (user, post, etc.)
                    $model = $parts[1];

                    // Group permissions by model and then by action
                    $groupedPermissions[$model][$action][] = $permission;
                    }
                    }
                    @endphp

                    <table class="table table-bordered">
                        <thead>
                            <tr style="border: 1px solid #cccccc;">
                                <th>Model</th>
                                <th>Create</th>
                                <th>Update</th>
                                <th>View</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedPermissions as $model => $actions)
                            <tr style="border: 1px solid #cccccc;">
                                <td>{{ ucfirst($model) }}</td>

                                <!-- Create Permission -->
                                <td>
                                    @if(isset($actions['create']))
                                    @foreach ($actions['create'] as $permission)

                                    <label class="checkbox">
                                        <input type="checkbox" name="permission[]" value="{{ $permission->name }}" {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }} />
                                        <span class="checkbox__marker">
                                            <span class="checkbox__marker-icon">
                                                <svg viewBox="0 0 14 12">
                                                    <path d="M11.7917 1.2358C12.0798 1.53914 12.0675 2.01865 11.7642 2.30682L5.7036 8.06439C5.40574 8.34735 4.93663 8.34134 4.64613 8.05084L2.22189 5.6266C1.92604 5.33074 1.92604 4.85107 2.22189 4.55522C2.51774 4.25937 2.99741 4.25937 3.29326 4.55522L5.19538 6.45734L10.7206 1.20834C11.024 0.920164 11.5035 0.93246 11.7917 1.2358Z" />
                                                </svg>
                                            </span>
                                        </span>
                                        &nbsp;{{ $permission->name }}
                                    </label>
                                    @endforeach
                                    @endif
                                </td>

                                <!-- Update Permission -->
                                <td>
                                    @if(isset($actions['update']))
                                    @foreach ($actions['update'] as $permission)
                                    <label class="checkbox">
                                        <input type="checkbox" name="permission[]" value="{{ $permission->name }}" {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }} />
                                        <span class="checkbox__marker">
                                            <span class="checkbox__marker-icon">
                                                <svg viewBox="0 0 14 12">
                                                    <path d="M11.7917 1.2358C12.0798 1.53914 12.0675 2.01865 11.7642 2.30682L5.7036 8.06439C5.40574 8.34735 4.93663 8.34134 4.64613 8.05084L2.22189 5.6266C1.92604 5.33074 1.92604 4.85107 2.22189 4.55522C2.51774 4.25937 2.99741 4.25937 3.29326 4.55522L5.19538 6.45734L10.7206 1.20834C11.024 0.920164 11.5035 0.93246 11.7917 1.2358Z" />
                                                </svg>
                                            </span>
                                        </span>
                                        &nbsp;{{ $permission->name }}
                                    </label>
                                    @endforeach
                                    @endif
                                </td>

                                <!-- View Permission -->
                                <td>
                                    @if(isset($actions['view']))
                                    @foreach ($actions['view'] as $permission)
                                    <label class="checkbox">
                                        <input type="checkbox" name="permission[]" value="{{ $permission->name }}" {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }} />
                                        <span class="checkbox__marker">
                                            <span class="checkbox__marker-icon">
                                                <svg viewBox="0 0 14 12">
                                                    <path d="M11.7917 1.2358C12.0798 1.53914 12.0675 2.01865 11.7642 2.30682L5.7036 8.06439C5.40574 8.34735 4.93663 8.34134 4.64613 8.05084L2.22189 5.6266C1.92604 5.33074 1.92604 4.85107 2.22189 4.55522C2.51774 4.25937 2.99741 4.25937 3.29326 4.55522L5.19538 6.45734L10.7206 1.20834C11.024 0.920164 11.5035 0.93246 11.7917 1.2358Z" />
                                                </svg>
                                            </span>
                                        </span>
                                        &nbsp;{{ $permission->name }}
                                    </label>
                                    @endforeach
                                    @endif
                                </td>

                                <!-- Delete Permission -->
                                <td>
                                    @if(isset($actions['delete']))
                                    @foreach ($actions['delete'] as $permission)
                                    <label class="checkbox">
                                        <input type="checkbox" name="permission[]" value="{{ $permission->name }}" {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }} />
                                        <span class="checkbox__marker">
                                            <span class="checkbox__marker-icon">
                                                <svg viewBox="0 0 14 12">
                                                    <path d="M11.7917 1.2358C12.0798 1.53914 12.0675 2.01865 11.7642 2.30682L5.7036 8.06439C5.40574 8.34735 4.93663 8.34134 4.64613 8.05084L2.22189 5.6266C1.92604 5.33074 1.92604 4.85107 2.22189 4.55522C2.51774 4.25937 2.99741 4.25937 3.29326 4.55522L5.19538 6.45734L10.7206 1.20834C11.024 0.920164 11.5035 0.93246 11.7917 1.2358Z" />
                                                </svg>
                                            </span>
                                        </span>
                                        &nbsp;{{ $permission->name }}
                                    </label>
                                    @endforeach
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mb-3 mt-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('roles.index') }}" class="btn btn-danger float-end">Back</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</main>

@endsection