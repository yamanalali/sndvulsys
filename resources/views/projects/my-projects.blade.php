@extends('layouts.master')

@section('content')
<div class="flex items-center justify-center min-h-screen" dir="rtl">
    <div class="max-w-7xl w-full mx-auto px-4 relative z-10 bg-white rounded-lg shadow-sm">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">مشاريعي</h1>
            <p class="text-slate-600 mt-1 text-sm">المشاريع التي أديرها أو أشارك فيها</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('projects.index') }}" 
               class="px-3 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-700 transition text-sm">
                جميع المشاريع
            </a>
            <a href="{{ route('projects.team-tasks') }}" 
               class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                مهام فريقي
            </a>
        </div>
    </div>

    <!-- Managed Projects -->
    @if($managedProjects->count() > 0)
        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-3 flex items-center">
                <svg class="w-4 h-4 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                المشاريع التي أديرها
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($managedProjects as $project)
                    @include('projects.partials.project-card', ['project' => $project, 'isManager' => true])
                @endforeach
            </div>
        </div>
    @endif

    <!-- Team Projects -->
    @if($teamProjects->count() > 0)
        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-3 flex items-center">
                <svg class="w-4 h-4 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                مشاريع الفريق
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($teamProjects as $project)
                    @include('projects.partials.project-card', ['project' => $project, 'isManager' => false])
                @endforeach
            </div>
        </div>
    @endif

    <!-- Assigned Projects -->
    @if($assignedProjects->count() > 0)
        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-3 flex items-center">
                <svg class="w-4 h-4 ml-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                المشاريع التي لدي مهام فيها
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($assignedProjects as $project)
                    @include('projects.partials.project-card', ['project' => $project, 'isManager' => false])
                @endforeach
            </div>
        </div>
    @endif

    <!-- Empty State -->
    @if($managedProjects->count() == 0 && $teamProjects->count() == 0 && $assignedProjects->count() == 0)
        <div class="text-center py-8">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-base font-medium text-slate-600 mb-1">لا توجد مشاريع لك</h3>
            <p class="text-slate-500 mb-4 text-sm">لم يتم تعيينك في أي مشروع بعد</p>
            <a href="{{ route('projects.index') }}" 
               class="inline-flex items-center px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition text-sm">
                عرض جميع المشاريع
            </a>
        </div>
    @endif
    </div>
</div>
@endsection 
