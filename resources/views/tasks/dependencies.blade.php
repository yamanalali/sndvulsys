@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Link a dependency to task: {{ $task->title }}</h2>

    {{-- Success alert --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form to add a task dependency --}}
    <form method="POST" action="{{ route('tasks.dependencies.store', $task->id) }}">
        @csrf

        <div class="form-group">
            <label for="depends_on_id">Select the task this one depends on:</label>
            <select name="depends_on_id" class="form-control" required>
                @foreach($allTasks as $t)
                    <option value="{{ $t->id }}">{{ $t->title }}</option>
                @endforeach
            </select>
        </div>

        <br>

        <button type="submit" class="btn btn-primary">Add Dependency</button>
    </form>
</div>
@endsection
