@extends('layouts.master')

@section('content')
<div class="flex items-center justify-center min-h-screen" dir="rtl">
    <div class="max-w-7xl w-full mx-auto px-4 relative z-10 bg-white rounded-lg shadow-sm">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">المشاريع</h1>
                <p class="text-slate-600 mt-0.5 text-sm">إدارة جميع المشاريع والمهام المرتبطة بها</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.my-projects') }}" 
                   class="px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                    مشاريعي
                </a>
                <a href="{{ route('projects.team-tasks') }}" 
                   class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                    مهام فريقي
                </a>
                <a href="{{ route('projects.create') }}" 
                   class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition font-bold text-sm">
                    مشروع جديد
                </a>
            </div>
        </div>

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($projects as $project)
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition">
                    <!-- Project Header -->
                    <div class="p-4 border-b border-slate-100">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-base font-bold text-slate-800">{{ $project->name }}</h3>
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $project->status_color }}">
                                {{ $project->status_label }}
                            </span>
                        </div>
                        
                        @if($project->description)
                            <p class="text-slate-600 text-xs line-clamp-2">{{ $project->description }}</p>
                        @endif
                    </div>

                    <!-- Project Stats -->
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="text-center">
                                <div class="text-lg font-bold text-slate-800">{{ $project->tasks->count() }}</div>
                                <div class="text-xs text-slate-500">المهام</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-green-600">{{ $project->progress }}%</div>
                                <div class="text-xs text-slate-500">التقدم</div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-200 rounded-full h-1.5 mb-3">
                            <div class="bg-green-500 h-1.5 rounded-full transition-all duration-300" 
                                 style="width: {{ $project->progress }}%"></div>
                        </div>

                        <!-- Project Info -->
                        <div class="space-y-1 text-xs">
                            @if($project->manager)
                                <div class="flex items-center text-slate-600">
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    المدير: {{ $project->manager->name }}
                                </div>
                            @endif
                            
                            @if($project->start_date)
                                <div class="flex items-center text-slate-600">
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    البداية: {{ $project->start_date->format('Y-m-d') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Project Actions -->
                    <div class="px-4 py-3 bg-slate-50 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('projects.show', $project) }}" 
                               class="text-primary hover:text-primary/80 font-medium text-xs">
                                عرض التفاصيل
                            </a>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('projects.edit', $project) }}" 
                                   class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-1.5 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded transition"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا المشروع؟')">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V4a1 1 0 011-1h6a1 1 0 011 1v3"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-6">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="text-base font-medium text-slate-600 mb-1">لا توجد مشاريع</h3>
                        <p class="text-slate-500 mb-4 text-sm">ابدأ بإنشاء مشروعك الأول</p>
                        <a href="{{ route('projects.create') }}" 
                           class="inline-flex items-center px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition text-sm">
                            إنشاء مشروع جديد
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection 