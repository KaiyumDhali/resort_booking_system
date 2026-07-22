{{-- _form.blade.php (shared form) --}}

@if($errors->any())
<div class="alert alert-danger mb-4">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body py-4">
        <div class="row g-4">

            {{-- Title --}}
            <div class="col-md-8">
                <label class="form-label fw-semibold required">Task Title</label>
                <input type="text" name="title" class="form-control"
                       placeholder="Task এর নাম লিখুন"
                       value="{{ old('title', $task->title ?? '') }}" required>
            </div>

            {{-- Priority --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold required">Priority</label>
                <select name="priority" class="form-select" required>
                    <option value="high"   {{ old('priority', $task->priority ?? '') == 'high'   ? 'selected' : '' }}>🔴 High</option>
                    <option value="medium" {{ old('priority', $task->priority ?? 'medium') == 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                    <option value="low"    {{ old('priority', $task->priority ?? '') == 'low'    ? 'selected' : '' }}>🟢 Low</option>
                </select>
            </div>

            {{-- Description --}}
            <div class="col-md-12">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="2"
                          placeholder="Task এর বিস্তারিত (optional)">{{ old('description', $task->description ?? '') }}</textarea>
            </div>

            {{-- Issue Date --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold required">Issue Date</label>
                <input type="date" name="issue_date" class="form-control"
                       value="{{ old('issue_date', isset($task) ? $task->issue_date->format('Y-m-d') : date('Y-m-d')) }}"
                       required>
            </div>

            {{-- End Date --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold required">End Date</label>
                <input type="date" name="end_date" class="form-control"
                       value="{{ old('end_date', isset($task) ? $task->end_date->format('Y-m-d') : '') }}"
                       required>
            </div>

            {{-- Budget --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold required">Budget (৳)</label>
                <input type="number" step="0.01" min="0" name="budget"
                       class="form-control" placeholder="0.00"
                       value="{{ old('budget', $task->budget ?? '') }}" required>
            </div>

            {{-- Remarks --}}
            <div class="col-md-12">
                <label class="form-label fw-semibold">Remarks</label>
                <input type="text" name="remarks" class="form-control"
                       placeholder="Optional..."
                       value="{{ old('remarks', $task->remarks ?? '') }}">
            </div>

        </div>
    </div>

    <div class="card-footer bg-white border-top d-flex gap-2 py-3">
        <button type="submit" class="btn btn-primary px-6">
            {{ isset($task) ? 'Update Task' : 'Save Task' }}
        </button>
        <a href="{{ route('tasks.index') }}" class="btn btn-light">Cancel</a>
    </div>
</div>