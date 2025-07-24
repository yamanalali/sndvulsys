@extends('layouts.master')

@section('content')
<div class="row mt-4">
    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="f-w-600 text-c-blue">Projects Board</h3>
            <p class="text-muted">List of your ongoing projects</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <i class="feather icon-plus"></i> New Project
        </a>
    </div>
    @forelse($projects as $project)
        <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
            <div class="card user-card">
                <div class="card-block">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <img class="img-fluid rounded" src="{{ asset('files/assets/images/object/object-1.jpg') }}" alt="image" style="width:48px;height:48px;">
                        <span class="badge badge-info">{{ $project->start_date ? date('M d, Y', strtotime($project->start_date)) : '-' }}</span>
                    </div>
                    <h5 class="f-w-600 text-c-blue">{{ $project->name }}</h5>
                    <p class="text-muted">{{ $project->description }}</p>
                    <div class="progress my-2" style="height:6px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ isset($project->progress) ? $project->progress : '0' }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="badge badge-light">{{ $project->end_date ? 'Ends: '.date('M d, Y', strtotime($project->end_date)) : '' }}</span>
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-info"><i class="feather icon-eye"></i></a>
                    </div>
                    @if($project->manager)
                        <div class="mt-3 d-flex align-items-center">
                            <img class="rounded-circle border border-white" src="{{ asset('files/assets/images/avatar/avatar-1.jpg') }}" alt="avatar" style="width:32px;height:32px;">
                            <span class="ml-2 text-muted">{{ $project->manager->name }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center py-4">لا توجد مشاريع حالياً</div>
        </div>
    @endforelse
</div>
@endsection 