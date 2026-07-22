{{-- edit.blade.php --}}
<x-default-layout>
 
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('tasks.index') }}" class="btn btn-light btn-sm">← Back</a>
    <h4 class="mb-0">Task Edit করুন</h4>
</div>
 
<form action="{{ route('tasks.update', $task->id) }}" method="POST">
@csrf @method('PUT')
@include('pages.tasks._form', ['task' => $task])
</form>
 
</x-default-layout>