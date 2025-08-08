<div class="sidebar print:hidden">
    <!-- Main Sidebar -->
    <div class="main-sidebar">
        <div class="flex h-full w-full flex-col items-center border-r border-slate-150 bg-white dark:border-navy-700 dark:bg-navy-800">
            <!-- Application Logo -->
            <div class="flex pt-4">
                <a href="{{ route('home') }}">
                    <img class="size-11 transition-transform duration-500 ease-in-out hover:rotate-[360deg]" src="{{ URL::to('assets/images/app-logo.png') }}" alt="logo">
                </a>
            </div>

            <!-- Main Sections Links -->
            <div class="is-scrollbar-hidden flex grow flex-col space-y-4 overflow-y-auto pt-6">
                <!-- Dashboards Only -->
                <a href="{{ route('home') }}"
                    class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-none transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
                    x-tooltip.placement.right="'Dashboards'">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path fill="currentColor" fill-opacity=".3" d="M5 14.059c0-1.01 0-1.514.222-1.945.221-.43.632-.724 1.453-1.31l4.163-2.974c.56-.4.842-.601 1.162-.601.32 0 .601.2 1.162.601l4.163 2.974c.821.586 1.232.88 1.453 1.31.222.43.222.935.222 1.945V19c0 .943 0 1.414-.293 1.707C18.414 21 17.943 21 17 21H7c-.943 0-1.414 0-1.707-.293C5 20.414 5 19.943 5 19v-4.94Z"> </path>
                        <path fill="currentColor" d="M3 12.387c0 .267 0 .4.084.441.084.041.19-.04.4-.204l7.288-5.669c.59-.459.885-.688 1.228-.688.343 0 .638.23 1.228.688l7.288 5.669c.21.163.316.245.4.204.084-.04.084-.174.084-.441v-.409c0-.48 0-.72-.102-.928-.101-.208-.291-.355-.67-.65l-7-5.445c-.59-.459-.885-.688-1.228-.688-.343 0-.638.23-1.228.688l-7 5.445c-.379.295-.569.442-.67.65-.102.208-.102.448-.102.928v.409Z"> </path>
                        <path fill="currentColor" d="M11.5 15.5h1A1.5 1.5 0 0 1 14 17v3.5h-4V17a1.5 1.5 0 0 1 1.5-1.5Z"></path>
                        <path fill="currentColor" d="M17.5 5h-1a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5Z"> </path>
                    </svg>
                </a>
                <!-- أيقونة المهام -->
                <a href="{{ route('tasks.index') }}"
                    class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-none transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
                    x-tooltip.placement.right="'المهام'">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <rect x="4" y="5" width="16" height="2" rx="1" fill="currentColor"/>
                        <rect x="4" y="11" width="16" height="2" rx="1" fill="currentColor"/>
                        <rect x="4" y="17" width="16" height="2" rx="1" fill="currentColor"/>
                    </svg>
                </a>
                <!-- أيقونة المهام المتكررة -->
                <a href="{{ route('recurring-tasks.index') }}"
                    class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-none transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
                    x-tooltip.placement.right="'المهام المتكررة'">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </a>
                <!-- أيقونة تقويم المواعيد -->
                <a href="{{ route('progress.calendar') }}"
                    class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-none transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
                    x-tooltip.placement.right="'تقويم المواعيد'">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </a>
                <!-- أيقونة تتبع التقدم -->
                <a href="{{ route('progress.index') }}"
                    class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-none transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
                    x-tooltip.placement.right="'تتبع التقدم'">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </a>
                <!-- أيقونة المشاريع -->
                <a href="{{ route('projects.index') }}"
                    class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-none transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
                    x-tooltip.placement.right="'المشاريع'">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </a>

                <!-- أيقونة المستندات -->
                <a href="{{ route('documents.index') }}"
                    class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-none transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
                    x-tooltip.placement.right="'المستندات'">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </a>
                <!-- أيقونة الإرسالات -->
                <a href="{{ route('submissions.index') }}"
                    class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-none transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
                    x-tooltip.placement.right="'الإرسالات'">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </a>
                <!-- أيقونة إدارة الحالات -->
                <a href="{{ route('case-management.index') }}"
                    class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-none transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
                    x-tooltip.placement.right="'إدارة الحالات'">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </a>
                <!-- أيقونة البحث المتقدم -->
                <a href="{{ route('advanced-search.index') }}"
                    class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-none transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
                    x-tooltip.placement.right="'البحث المتقدم'">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>

                    </svg>
                </a>
                <a href="{{ route('register') }}"
                    class="flex size-11 items-center justify-center rounded-lg bg-primary/10 text-primary outline-none transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-navy-600 dark:text-accent-light dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
                    x-tooltip.placement.right="'Dashboards'">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="currentColor" fill-opacity="0.1"/>
                        <path d="M12 7v5l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            <!-- Bottom Links -->
            <div class="flex flex-col items-center space-y-3 py-3">
                <!-- Settings -->
                <a href="form-layout-5.html"
                    class="flex size-11 items-center justify-center rounded-lg outline-none transition-colors duration-200 hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                    <svg class="h-7 w-7" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-opacity="0.3" fill="currentColor"
                            d="M2 12.947v-1.771c0-1.047.85-1.913 1.899-1.913 1.81 0 2.549-1.288 1.64-2.868a1.919 1.919 0 0 1 .699-2.607l1.729-.996c.79-.474 1.81-.192 2.279.603l.11.192c.9 1.58 2.379 1.58 3.288 0l.11-.192c.47-.795 1.49-1.077 2.279-.603l1.73.996a1.92 1.92 0 0 1 .699 2.607c-.91 1.58-.17 2.868 1.639 2.868 1.04 0 1.899.856 1.899 1.912v1.772c0 1.047-.85 1.912-1.9 1.912-1.808 0-2.548 1.288-1.638 2.869.52.915.21 2.083-.7 2.606l-1.729.997c-.79.473-1.81.191-2.279-.604l-.11-.191c-.9-1.58-2.379-1.58-3.288 0l-.11.19c-.47.796-1.49 1.078-2.279.605l-1.73-.997a1.919 1.919 0 0 1-.699-2.606c.91-1.58.17-2.869-1.639-2.869A1.911 1.911 0 0 1 2 12.947Z">
                        </path>
                        <path fill="currentColor"
                            d="M11.995 15.332c1.794 0 3.248-1.464 3.248-3.27 0-1.807-1.454-3.272-3.248-3.272-1.794 0-3.248 1.465-3.248 3.271 0 1.807 1.454 3.271 3.248 3.271Z">
                        </path>
                    </svg>
                </a>

                <!-- Profile -->
                <div x-data="usePopper({placement:'right-end',offset:12})"
                    @click.outside="isShowPopper && (isShowPopper = false)" class="flex">
                    <button @click="isShowPopper = !isShowPopper" x-ref="popperRef" class="avatar size-12">
                        <img class="rounded-full" src="{{ URL::to('assets/images/avatar/avatar-12.jpg') }}" alt="avatar">
                        <span
                            class="absolute right-0 size-3.5 rounded-full border-2 border-white bg-success dark:border-navy-700"></span>
                    </button>

                    <div :class="isShowPopper && 'show'" class="popper-root fixed" x-ref="popperRoot">
                        <div
                            class="popper-box w-64 rounded-lg border border-slate-150 bg-white shadow-soft dark:border-navy-600 dark:bg-navy-700">
                            <div
                                class="flex items-center space-x-4 rounded-t-lg bg-slate-100 py-5 px-4 dark:bg-navy-800">
                                <div class="avatar size-14">
                                    <img class="rounded-full" src="{{ URL::to('assets/images/avatar/avatar-12.jpg') }}" alt="avatar">
                                </div>
                                <div>
                                    <a href="#" class="text-base font-medium text-slate-700 hover:text-primary focus:text-primary dark:text-navy-100 dark:hover:text-accent-light dark:focus:text-accent-light">
                                        StarCodeKh
                                    </a>
                                    <p class="text-xs text-slate-400 dark:text-navy-300">
                                        Full-Stack Developer
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-col pt-2 pb-5">
                                <a href="#"
                                    class="group flex items-center space-x-3 py-2 px-4 tracking-wide outline-none transition-all hover:bg-slate-100 focus:bg-slate-100 dark:hover:bg-navy-600 dark:focus:bg-navy-600">
                                    <div
                                        class="flex size-8 items-center justify-center rounded-lg bg-warning text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none"
                                            viewbox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h2
                                            class="font-medium text-slate-700 transition-colors group-hover:text-primary group-focus:text-primary dark:text-navy-100 dark:group-hover:text-accent-light dark:group-focus:text-accent-light">
                                            Profile
                                        </h2>
                                        <div class="text-xs text-slate-400 line-clamp-1 dark:text-navy-300">
                                            Your profile setting
                                        </div>
                                    </div>
                                </a>
                                <a href="#"
                                    class="group flex items-center space-x-3 py-2 px-4 tracking-wide outline-none transition-all hover:bg-slate-100 focus:bg-slate-100 dark:hover:bg-navy-600 dark:focus:bg-navy-600">
                                    <div
                                        class="flex size-8 items-center justify-center rounded-lg bg-info text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none"
                                            viewbox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                                            </path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h2
                                            class="font-medium text-slate-700 transition-colors group-hover:text-primary group-focus:text-primary dark:text-navy-100 dark:group-hover:text-accent-light dark:group-focus:text-accent-light">
                                            Messages
                                        </h2>
                                        <div class="text-xs text-slate-400 line-clamp-1 dark:text-navy-300">
                                            Your messages and tasks
                                        </div>
                                    </div>
                                </a>
                                <a href="#"
                                    class="group flex items-center space-x-3 py-2 px-4 tracking-wide outline-none transition-all hover:bg-slate-100 focus:bg-slate-100 dark:hover:bg-navy-600 dark:focus:bg-navy-600">
                                    <div
                                        class="flex size-8 items-center justify-center rounded-lg bg-secondary text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none"
                                            viewbox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h2
                                            class="font-medium text-slate-700 transition-colors group-hover:text-primary group-focus:text-primary dark:text-navy-100 dark:group-hover:text-accent-light dark:group-focus:text-accent-light">
                                            Team
                                        </h2>
                                        <div class="text-xs text-slate-400 line-clamp-1 dark:text-navy-300">
                                            Your team activity
                                        </div>
                                    </div>
                                </a>
                                <a href="#"
                                    class="group flex items-center space-x-3 py-2 px-4 tracking-wide outline-none transition-all hover:bg-slate-100 focus:bg-slate-100 dark:hover:bg-navy-600 dark:focus:bg-navy-600">
                                    <div
                                        class="flex size-8 items-center justify-center rounded-lg bg-error text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none"
                                            viewbox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h2
                                            class="font-medium text-slate-700 transition-colors group-hover:text-primary group-focus:text-primary dark:text-navy-100 dark:group-hover:text-accent-light dark:group-focus:text-accent-light">
                                            Activity
                                        </h2>
                                        <div class="text-xs text-slate-400 line-clamp-1 dark:text-navy-300">
                                            Your activity and events
                                        </div>
                                    </div>
                                </a>
                                <a href="#"
                                    class="group flex items-center space-x-3 py-2 px-4 tracking-wide outline-none transition-all hover:bg-slate-100 focus:bg-slate-100 dark:hover:bg-navy-600 dark:focus:bg-navy-600">
                                    <div
                                        class="flex size-8 items-center justify-center rounded-lg bg-success text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none"
                                            viewbox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                            </path>
                                        </svg>
                                    </div>

                                    <div>
                                        <h2
                                            class="font-medium text-slate-700 transition-colors group-hover:text-primary group-focus:text-primary dark:text-navy-100 dark:group-hover:text-accent-light dark:group-focus:text-accent-light">
                                            Settings
                                        </h2>
                                        <div class="text-xs text-slate-400 line-clamp-1 dark:text-navy-300">
                                            Webapp settings
                                        </div>
                                    </div>
                                </a>
                                <div class="mt-3 px-4">
                                    <a href="{{ route('logout') }}">
                                        <button class="btn h-9 w-full space-x-2 bg-primary text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none"
                                                viewbox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                            <span>Logout</span>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Panel -->
    <div class="sidebar-panel">
        <div class="flex h-full grow flex-col bg-white pl-[var(--main-sidebar-width)] dark:bg-navy-750">
            <!-- Sidebar Panel Header -->
            <div class="flex h-18 w-full items-center justify-between pl-4 pr-1">
                <p class="text-base tracking-wider text-slate-800 dark:text-navy-100">Dashboards</p>
                <button @click="$store.global.isSidebarExpanded = false" class="btn h-7 w-7 rounded-full p-0 text-primary hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:text-accent-light/80 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25 xl:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewbox="0 0 24 24"stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Panel Body -->
            <div x-data="{expandedItem:null}" class="h-[calc(100%-4.5rem)] overflow-x-hidden pb-6"
                x-init="$el._x_simplebar = new SimpleBar($el);">
                <ul class="flex flex-1 flex-col px-4 font-inter">
                    <li>
                        <a x-data="navLink" href="{{ route('home') }}" :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'" class="flex py-2 text-xs+ tracking-wide outline-none transition-colors duration-300 ease-in-out">
                            Orders
                        </a>
                    </li>
                </ul>
                <div class="my-3 mx-4 h-px bg-slate-200 dark:bg-navy-500"></div>
                <!-- روابط إجراءات المهام تظهر فقط في صفحة المهام -->
                @if(request()->is('tasks') || request()->is('tasks/*'))
                <ul class="flex flex-1 flex-col px-4 font-inter mb-2">
                    <li>
                        <a href="{{ route('tasks.index') }}" class="flex py-2 text-xs+ tracking-wide outline-none transition-colors duration-300 ease-in-out text-slate-600 hover:text-primary">
                            كل المهام
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tasks.create') }}" class="flex py-2 text-xs+ tracking-wide outline-none transition-colors duration-300 ease-in-out text-slate-600 hover:text-primary">
                            إضافة مهمة جديدة
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tasks.index', ['filter' => 'my']) }}" class="flex py-2 text-xs+ tracking-wide outline-none transition-colors duration-300 ease-in-out text-slate-600 hover:text-primary">
                            مهامي
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('tasks.index', ['filter' => 'completed']) }}" class="flex py-2 text-xs+ tracking-wide outline-none transition-colors duration-300 ease-in-out text-slate-600 hover:text-primary">
                            المهام المنجزة
                        </a>
                    </li>
                </ul>
                <div class="my-3 mx-4 h-px bg-slate-200 dark:bg-navy-500"></div>
                @endif
                <ul class="flex flex-1 flex-col px-4 font-inter">
                    <li>
                        <a x-data="navLink" href="dashboards-personal.html" :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'" class="flex py-2 text-xs+ tracking-wide outline-none transition-colors duration-300 ease-in-out">
                            Dashboard 01
                        </a>
                    </li>
                    <li>
                        <a x-data="navLink" href="dashboards-personal.html" :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'" class="flex py-2 text-xs+ tracking-wide outline-none transition-colors duration-300 ease-in-out">
                            Dashboard 02
                        </a>
                    </li>
                    <li>
                        <a x-data="navLink" href="dashboards-personal.html" :class="isActive ? 'font-medium text-primary dark:text-accent-light' : 'text-slate-600 hover:text-slate-900 dark:text-navy-200 dark:hover:text-navy-50'" class="flex py-2 text-xs+ tracking-wide outline-none transition-colors duration-300 ease-in-out">
                            Dashboard #
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>