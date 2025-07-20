<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition">
    <!-- Project Header -->
    <div class="p-6 border-b border-slate-100">
        <div class="flex items-start justify-between mb-3">
            <h3 class="text-lg font-bold text-slate-800">{{ $project->name }}</h3>
            <div class="flex items-center gap-2">
                @if($isManager ?? false)
                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-bold">مدير</span>
                @endif
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $project->status_color }}">
                    {{ $project->status_label }}
                </span>
            </div>
        </div>
        
        @if($project->description)
            <p class="text-slate-600 text-sm line-clamp-2">{{ $project->description }}</p>
        @endif
    </div>

    <!-- Project Stats -->
    <div class="p-6">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-slate-800">{{ $project->tasks->count() }}</div>
                <div class="text-xs text-slate-500">المهام</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $project->progress }}%</div>
                <div class="text-xs text-slate-500">التقدم</div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-slate-200 rounded-full h-2 mb-4">
            <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                 style="width: {{ $project->progress }}%"></div>
        </div>

        <!-- Project Info -->
        <div class="space-y-2 text-sm">
            @if($project->manager)
                <div class="flex items-center text-slate-600">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    المدير: {{ $project->manager->name }}
                </div>
            @endif
            
            @if($project->start_date)
                <div class="flex items-center text-slate-600">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    البداية: {{ $project->start_date->format('Y-m-d') }}
                </div>
            @endif

            @if($project->teamMembers->count() > 0)
                <div class="flex items-center text-slate-600">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    الفريق: {{ $project->teamMembers->count() }} عضو
                </div>
            @endif
        </div>
    </div>

    <!-- Project Actions -->
    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
        <div class="flex items-center justify-between">
            <a href="{{ route('projects.show', $project) }}" 
               class="text-primary hover:text-primary/80 font-medium text-sm">
                عرض التفاصيل
            </a>
            <div class="flex items-center gap-2">
                @if($isManager ?? false)
                    <a href="{{ route('projects.edit', $project) }}" 
                       class="p-2 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </a>
                    <form action="{{ route('projects.destroy', $project) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="p-2 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                onclick="return confirm('هل أنت متأكد من حذف هذا المشروع؟')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V4a1 1 0 011-1h6a1 1 0 011 1v3"></path>
                            </svg>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div> 