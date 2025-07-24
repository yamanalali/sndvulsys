@extends('layouts.master')

@section('content')
<main class="main-content w-full px-[var(--margin-x)] pb-8">
    <div class="mt-6 flex flex-col items-center justify-between space-y-2 text-center sm:flex-row sm:space-y-0 sm:text-left">
      <div>
        <h3 class="text-xl font-semibold text-slate-700 dark:text-navy-100">
          Projects Board
        </h3>
        <p class="mt-1 hidden sm:block">List of your ongoing projects</p>
      </div>
      <a href="{{ route('projects.create') }}" class="btn space-x-2 bg-primary font-medium text-white shadow-lg shadow-primary/50 hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:shadow-accent/50 dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-indigo-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        <span> New Project </span>
      </a>
    </div>
    <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6 xl:grid-cols-4">
      @forelse($projects as $project)
        <div class="card shadow-none">
          <div class="flex flex-1 flex-col justify-between rounded-lg bg-info/15 p-4 dark:bg-transparent sm:p-5">
            <div>
              <div class="flex items-start justify-between">
                <img class="size-12 rounded-lg object-cover object-center" src="{{ asset('images/others/smartphone.jpg') }}" alt="image">
                <p class="text-xs+">{{ $project->start_date ? date('M d, Y', strtotime($project->start_date)) : '-' }}</p>
              </div>
              <h3 class="mt-3 font-medium text-slate-700 line-clamp-2 dark:text-navy-100">
                {{ $project->name }}
              </h3>
              <p class="text-xs+">{{ $project->description }}</p>
            </div>
            <div>
              <div class="mt-4">
                <p class="text-xs+ text-slate-700 dark:text-navy-100">
                  Progress
                </p>
                <div class="progress my-2 h-1.5 bg-info/15 dark:bg-info/25">
                  <div class="w-{{ isset($project->progress) ? $project->progress : '0' }}/12 rounded-full bg-info"></div>
                </div>
                <p class="text-right text-xs+ font-medium text-info">
                  {{ isset($project->progress) ? $project->progress.'%' : '0%' }}
                </p>
              </div>
              <div class="mt-5 flex flex-wrap -space-x-3">
                @if($project->manager)
                  <div class="avatar size-8 hover:z-10">
                    <img class="rounded-full border-2 border-white dark:border-navy-700" src="{{ asset('images/avatar/avatar-1.jpg') }}" alt="avatar">
                  </div>
                  <div class="avatar size-8 hover:z-10">
                    <div class="is-initial rounded-full border-2 border-white bg-warning text-xs+ uppercase text-white dark:border-navy-700">
                      {{ Str::substr($project->manager->name, 0, 2) }}
                    </div>
                  </div>
                @endif
              </div>
              <div class="mt-4 flex items-center justify-between space-x-2">
                <div class="badge h-5.5 rounded-full bg-info px-2 text-xs+ text-white">
                  {{ $project->end_date ? 'Ends: '.date('M d, Y', strtotime($project->end_date)) : '' }}
                </div>
                <div>
                  <a href="{{ route('projects.show', $project) }}" class="btn -mr-1.5 size-8 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-span-4">
          <div class="text-center py-10 text-slate-500">لا توجد مشاريع حالياً</div>
        </div>
      @endforelse
    </div>
</main>
@endsection 