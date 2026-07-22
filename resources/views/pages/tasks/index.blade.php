<x-default-layout>
<style>
#taskTable{
    table-layout:auto;
}

#taskTable th{
    white-space:nowrap;
    vertical-align:middle;
}

#taskTable td{
    vertical-align:middle;
}

#taskTable .action-buttons{
    display: flex;
    flex-wrap: nowrap;
    justify-content: center;
    align-items: center;
    gap: .25rem;
    white-space: nowrap;
}

#taskTable .action-column{
    width: 1%;
    white-space: nowrap;
}

#taskTable .action-buttons .btn{
    flex-shrink: 0;
}

#taskTable .badge{
    font-weight:500;
}

#taskTable .assigned-list{
    display:flex;
    flex-wrap:wrap;
    gap:4px;
}

#taskTable .assigned-list .badge{
    margin:0;
}

#taskTable .title-cell{
    min-width:220px;
}

#taskTable .money{
    white-space:nowrap;
}

/* ✅ Table-কে নিজের ভেতরেই horizontal scroll দেওয়ার জন্য wrapper */
.table-scroll-wrapper{
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width:1200px){
    #taskTable{
        min-width:1200px;
    }
}

/* ✅ Mobile responsive adjustments */
@media (max-width: 767.98px){
    /* Summary cards — 2 per row বজায় থাকবে কিন্তু ছোট হবে */
    .fs-2{
        font-size: 1.4rem !important;
    }

    /* Card body padding কমানো */
    .card-body.py-4{
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }

    /* Filter row — প্রতিটা input ফুল-উইথ, ২টা করে এক লাইনে */
    .row.g-2.align-items-end > [class*="col-"]{
        flex: 0 0 50%;
        max-width: 50%;
        margin-bottom: .5rem;
    }

    /* Filter/Clear বাটন ফুল উইথ */
    .row.g-2.align-items-end > .col-md-2:last-child{
        flex: 0 0 100%;
        max-width: 100%;
    }

    /* Card header — title আর buttons স্ট্যাক হবে */
    .card-header.d-flex{
        flex-direction: column;
        align-items: stretch !important;
        gap: .5rem;
    }

    .card-header.d-flex .d-flex{
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .card-header.d-flex .d-flex .btn{
        flex: 1 1 auto;
    }

    /* Modal — ছোট স্ক্রিনে ফুল উইথ ও ভালোভাবে scroll হবে */
    .modal-dialog{
        margin: .5rem;
    }
}

@media (max-width: 400px){
    /* খুবই ছোট স্ক্রিনে summary card এক কলামে */
    .col-6.col-md-2{
        flex: 0 0 50%;
        max-width: 50%;
    }
}
</style>
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-3">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- SUMMARY CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-gray-800">{{ $summary->total ?? 0 }}</div>
            <div class="text-muted fs-7">Total</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-warning">{{ $summary->pending ?? 0 }}</div>
            <div class="text-muted fs-7">Pending</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-primary">{{ $summary->approved ?? 0 }}</div>
            <div class="text-muted fs-7">Approved</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-success">{{ $summary->completed ?? 0 }}</div>
            <div class="text-muted fs-7">Completed</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-danger">{{ $summary->cancelled ?? 0 }}</div>
            <div class="text-muted fs-7">Cancelled</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-2 fw-bold text-info">৳ {{ number_format($summary->total_spent ?? 0, 0) }}</div>
            <div class="text-muted fs-7">Total Spent</div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
       <form method="GET" action="{{ route('tasks.index') }}">
    <div class="row g-2 align-items-end">
        <div class="col-md-2">
            <label class="form-label fs-7 mb-1">From Date</label>
            <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label fs-7 mb-1">To Date</label>
            <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label fs-7 mb-1">Month</label>
            <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label fs-7 mb-1">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="pending"   {{ request('status')=='pending'  ?'selected':'' }}>Pending</option>
                <option value="approved"  {{ request('status')=='approved' ?'selected':'' }}>Approved</option>
                <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fs-7 mb-1">Priority</label>
            <select name="priority" class="form-select form-select-sm">
                <option value="">All Priority</option>
                <option value="high"   {{ request('priority')=='high'  ?'selected':'' }}>High</option>
                <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
                <option value="low"    {{ request('priority')=='low'   ?'selected':'' }}>Low</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">Filter</button>
            <a href="{{ route('tasks.index') }}" class="btn btn-light btn-sm">✕</a>
        </div>
    </div>

    
</form>
    </div>
</div>

{{-- TABLE --}}
<div class="card border-0 shadow-sm">
    
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-semibold">Task List</h5>
        <div class="d-flex gap-2">
            <!-- <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#carryForwardModal">
                ↩ Carry Forward
            </button> -->
          
  
            <a href="{{ route('tasks.index', array_merge(request()->except('page'), ['my_task' => 1])) }}"
               class="btn btn-sm {{ request('my_task') ? 'btn-primary' : 'btn-outline-primary' }}">
                👤 My Task
            </a>

            @if(request('my_task'))
                <a href="{{ route('tasks.index', request()->except(['my_task', 'page'])) }}"
                   class="btn btn-sm btn-light ms-1">
                    All Task
                </a>
            @endif
    
            <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">+ New Task</a>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-scroll-wrapper">
            <table class="table table-hover align-middle table-row-bordered mb-0" id="taskTable">
                <thead class="table-light">
                    <tr class="fw-semibold text-uppercase fs-8">
                        <th style="width:50px">#</th>
                        <th style="min-width:220px">Title</th>
                        <th style="width:110px">Issue</th>
                        <th style="width:110px">End</th>
                        <th style="width:120px" class="text-end">Budget</th>
                        <th style="width:120px" class="text-end">Spent</th>
                        <th style="width:90px" class="text-center">Priority</th>
                        <th style="min-width:180px">Assigned</th>
                        <th style="width:110px" class="text-center">Status</th>
                        <th class="text-center action-column">Action</th>
                    </tr>
                    </thead>
                <tbody>
                @forelse($tasks as $task)
                <tr style="border-bottom: 1px solid #f1f1f4;">
                    <td class="ps-4 text-muted fs-7">{{ $loop->iteration }}</td>
                    <td>
                        <div class="fw-semibold text-gray-800">{{ $task->title }}</div>
                        @if($task->description && $task->description !== 'N/A')
                            <div class="text-muted fs-7 mt-1">{{ Str::limit($task->description, 60) }}</div>
                        @endif
                        @if($task->is_carried_forward && $task->carriedFrom)
                            <span class="badge badge-light-warning fs-8 mt-1">
                                ↩ Carried from {{ $task->carriedFrom->end_date->format('M Y') }}
                            </span>
                        @endif
                        @if($task->is_overdue)
                            <span class="badge badge-light-danger fs-8 mt-1">⚠ Overdue</span>
                        @endif
                    </td>
                    <td class="fs-7 text-muted">{{ $task->issue_date->format('d M Y') }}</td>
                    <td class="fs-7 text-muted">{{ $task->end_date->format('d M Y') }}</td>
                    <td class="text-end fw-semibold text-gray-800">৳ {{ number_format($task->budget, 0) }}</td>
                    <td class="text-end fw-semibold {{ $task->actual_cost > $task->budget ? 'text-danger' : 'text-success' }}">
                        {{ $task->actual_cost > 0 ? '৳ '.number_format($task->actual_cost, 0) : '—' }}
                    </td>
                    <td class="text-center">
                        @if($task->priority === 'high')
                            <span class="badge badge-light-danger">High</span>
                        @elseif($task->priority === 'medium')
                            <span class="badge badge-light-warning">Medium</span>
                        @else
                            <span class="badge badge-light-success">Low</span>
                        @endif
                    </td>
                
                    {{-- ✅ Assigned users --}}
                        <td class="fs-7">
                            @forelse($task->users as $user)
                                <span class="badge badge-light-info mb-1">{{ $user->name }}</span>
                            @empty
                                <span class="text-muted">—</span>
                            @endforelse
                        </td>
                    <td class="text-center">
                        @if($task->status === 'completed')
                            <span class="badge badge-light-success">Completed</span>
                            @if($task->completion_date)
                                <div class="text-muted fs-8 mt-1">{{ $task->completion_date->format('d M') }}</div>
                            @endif
                        @elseif($task->status === 'approved')
                            <span class="badge badge-light-primary">Approved</span>
                        @elseif($task->status === 'cancelled')
                            <span class="badge badge-light-danger">Cancelled</span>
                        @else
                            <span class="badge badge-light-warning">Pending</span>
                        @endif
                    </td>
                <td class="text-center">
                    <div class="action-buttons">

                                    
                        @can('read task approve')
                        @if($task->status === 'pending')

                        <button type="button" class="btn btn-xs btn-light-primary"
                            onclick="openApproveModal({{ $task->id }}, '{{ addslashes($task->title) }}', {{ $task->users->pluck('id') }})">
                            ✓ Approve
                        </button>

                        <button type="button" class="btn btn-xs btn-light-danger"
                            onclick="if(confirm('Task cancel করবেন?')) document.getElementById('cancelForm{{ $task->id }}').submit();">
                            ✕ Cancel
                        </button>
                        @endif
                        @endcan

                        {{-- Approved → Reassign / Cancel / Done --}}
                        @if($task->status === 'approved')
                        @can('read task approve')
                        <button type="button" class="btn btn-xs btn-light-info"
                            onclick="openApproveModal({{ $task->id }}, '{{ addslashes($task->title) }}', {{ $task->users->pluck('id') }})">
                            👤 Reassign
                        </button>

                        <button type="button" class="btn btn-xs btn-light-danger"
                            onclick="if(confirm('Are You Sure Cancel The Task?')) document.getElementById('cancelForm{{ $task->id }}').submit();">
                            ✕ Cancel
                        </button>
                        @endcan

                        @can('read task completed')
                        <button type="button" class="btn btn-xs btn-light-success"
                            onclick="openCompleteModal({{ $task->id }}, '{{ addslashes($task->title) }}', {{ $task->budget }}, '{{ addslashes($task->remarks ?? '') }}')">
                            Completed
                        </button>
                        @endcan
                        @endif

                            {{-- Cancelled → Reopen --}}
                            @if($task->status === 'cancelled')
                            <button type="button" class="btn btn-xs btn-light-warning"
                                onclick="if(confirm('Are You Sure Pending The Task?')) document.getElementById('pendingForm{{ $task->id }}').submit();">
                                ↺ Reopen
                            </button>
                            @endif

                            {{-- Pending task carry forward (single) --}}
                            @can('read carry forward')
                        @if(in_array($task->status, ['pending', 'approved']))
                            <button type="button" class="btn btn-xs btn-light-warning"
                                onclick="openSingleCarryModal({{ $task->id }}, '{{ addslashes($task->title) }}')">
                                ↩
                            </button>
                            @endif
                            @endcan
                            @if($task->status === 'pending')
                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-xs btn-light">✏</a>

                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                onsubmit="return confirm('Want to delete?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-light-danger">✕</button>
                            </form>
                            @endif
                            {{-- hidden helper forms --}}
                            <form id="cancelForm{{ $task->id }}" action="{{ route('tasks.cancel', $task->id) }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            <form id="pendingForm{{ $task->id }}" action="{{ route('tasks.mark-pending', $task->id) }}" method="POST" class="d-none">
                                @csrf
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5 text-muted">
                        Data not found.
                        <a href="{{ route('tasks.create') }}">+ New Task</a>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($tasks->hasPages())
    <div class="card-footer bg-white border-top-0 pt-3">
        {{ $tasks->links() }}
    </div>
    @endif
</div>

{{-- COMPLETE MODAL --}}
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="completeForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-semibold">Task Complete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-semibold text-gray-800 mb-4" id="completeTaskTitle"></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Actual Cost (৳)</label>
                        <input type="number" step="0.01" name="actual_cost" id="actualCost" class="form-control" required>
                        <small class="text-muted" id="budgetHint"></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Remarks</label>
                        <textarea name="remarks" id="completeRemarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Complete</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ✅ APPROVE / ASSIGN USER MODAL --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="approveForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-semibold">✓ Task Approve & Assign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-semibold text-gray-800 mb-3" id="approveTaskTitle"></p>
                    <label class="form-label fw-semibold fs-7">Select The User.</label>
                    <div class="border rounded p-2" style="max-height: 260px; overflow-y: auto;">
                        @forelse($users as $user)
                        <div class="form-check py-1">
                            <input class="form-check-input approve-user-checkbox" type="checkbox"
                                   name="user_ids[]" value="{{ $user->id }}"
                                   id="approveUser{{ $user->id }}">
                            <label class="form-check-label" for="approveUser{{ $user->id }}">
                                {{ $user->name }}
                                @if($user->email)
                                    <span class="text-muted fs-8">({{ $user->email }})</span>
                                @endif
                            </label>
                        </div>
                        @empty
                        <p class="text-muted mb-0">Not found active user.</p>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign & Approve</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- CARRY FORWARD MODAL --}}
<div class="modal fade" id="carryForwardModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('tasks.carry-forward') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-semibold">↩ Carry Forward</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted fs-7 mb-4">
                        যে মাসের pending tasks forward করতে চান সেই মাস select করুন এবং নতুন date range দিন।
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Source Month <span class="text-danger">*</span></label>
                        <input type="month" name="month" class="form-control" value="{{ date('Y-m') }}" required>
                        <small class="text-muted">এই মাসের pending/approved tasks carry হবে</small>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold fs-7">New Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="new_start_date" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold fs-7">New End Date <span class="text-danger">*</span></label>
                            <input type="date" name="new_end_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">↩ Carry Forward</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- SINGLE TASK CARRY FORWARD MODAL --}}
<div class="modal fade" id="singleCarryModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="singleCarryForm" action="{{ route('tasks.carry-forward-single') }}" method="POST">
            @csrf
            <input type="hidden" name="task_id" id="singleCarryTaskId">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-semibold">↩ Carry Forward</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-semibold text-gray-800 mb-4" id="singleCarryTaskTitle"></p>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold fs-7">New Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="new_start_date" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold fs-7">New End Date <span class="text-danger">*</span></label>
                            <input type="date" name="new_end_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">↩ Carry Forward</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openCompleteModal(id, title, budget, remarks) {
    document.getElementById('completeTaskTitle').innerText = title;
    document.getElementById('actualCost').value = budget;
    document.getElementById('budgetHint').innerText = 'Budget: ৳ ' + parseFloat(budget).toLocaleString();
    document.getElementById('completeRemarks').value = remarks || '';
    document.getElementById('completeForm').action = '/tasks/' + id + '/complete';
    new bootstrap.Modal(document.getElementById('completeModal')).show();
}

function openSingleCarryModal(id, title) {
    document.getElementById('singleCarryTaskId').value = id;
    document.getElementById('singleCarryTaskTitle').innerText = title;
    new bootstrap.Modal(document.getElementById('singleCarryModal')).show();
}

// ✅ Approve modal — task-এর জন্য employee checkbox select করে assign
// ✅ Approve modal — task-এর জন্য user checkbox select করে assign
function openApproveModal(id, title, currentUserIds) {
    document.getElementById('approveTaskTitle').innerText = title;
    document.getElementById('approveForm').action = '/tasks/' + id + '/approve';

    // সব checkbox আগে uncheck করে দিন
    document.querySelectorAll('.approve-user-checkbox').forEach(function (cb) {
        cb.checked = false;
    });

    // যদি এই task-এ আগে থেকে কোনো user assign করা থাকে, সেগুলো pre-check করুন (Reassign-এর সময় কাজে লাগবে)
    if (Array.isArray(currentUserIds)) {
        currentUserIds.forEach(function (userId) {
            var checkbox = document.getElementById('approveUser' + userId);
            if (checkbox) checkbox.checked = true;
        });
    }

    new bootstrap.Modal(document.getElementById('approveModal')).show();
}
</script>

</x-default-layout>