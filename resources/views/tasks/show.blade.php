@extends('layouts.app') 

@section('content')
<div class="container">
    <h2>Task Details</h2>

    <p><strong>Title:</strong> {{ $task->title }}</p>
    <p><strong>Description:</strong> {{ $task->description }}</p>
    <p><strong>Current Status:</strong> {{ $task->status }}</p>

    <hr>

    <h4>Update Status</h4>
    <form method="POST" action="{{ route('tasks.updateStatus', $task->id) }}">
        @csrf
        <label for="status">New Status:</label>
        <select name="status" class="form-control" required>
            <option value="in_progress">In Progress</option>
            <option value="pending_review">Pending Review</option>
            <option value="awaiting_approval">Awaiting Approval</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="on_hold">On Hold</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
            <option value="archived">Archived</option>
        </select>
        <br>
        <button type="submit" class="btn btn-primary">Update Status</button>
    </form>
</div>
@endsection
