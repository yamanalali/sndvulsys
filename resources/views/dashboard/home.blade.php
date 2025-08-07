@extends('layouts.master')
@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Welcome to the Dashboard</h4>
            </div>
            <div class="card-body">
                <p class="lead">Hello, <strong>{{ Auth::user()?->name ?? 'Guest' }}</strong>!</p>
                <p>You're logged in. This is your dashboard home page. Use the sidebar to navigate through the system features.</p>
                <hr>
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <i class="icofont icofont-users-alt-5 fa-2x text-primary mb-2"></i>
                                <h6 class="mb-0">Users</h6>
                                <span class="text-muted small">Manage users</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <i class="icofont icofont-tasks fa-2x text-success mb-2"></i>
                                <h6 class="mb-0">Tasks</h6>
                                <span class="text-muted small">View and assign tasks</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <i class="icofont icofont-chart-bar-graph fa-2x text-info mb-2"></i>
                                <h6 class="mb-0">Reports</h6>
                                <span class="text-muted small">View reports</span>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
