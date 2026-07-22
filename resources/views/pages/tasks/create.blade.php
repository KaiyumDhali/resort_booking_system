{{-- create.blade.php --}}
<x-default-layout>
 
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('tasks.index') }}" class="btn btn-light btn-sm">← Back</a>
    <h4 class="mb-0">নতুন Task তৈরি করুন</h4>
</div>
 
<form action="{{ route('tasks.store') }}" method="POST">
@csrf
@include('pages.tasks._form')
</form>
 
</x-default-layout>