<div class="pcoded-main-container">
            <div class="pcoded-wrapper">
                <nav class="pcoded-navbar">
                    <div class="pcoded-inner-navbar main-menu">
                        <div class="pcoded-navigatio-lavel">Dashboard</div>
                        <ul class="pcoded-item pcoded-left-item">
                            <li>
                                <a href="{{ route('home') }}">
                                    <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                                    <span class="pcoded-mtext">Dashboard</span>
                                </a>
                            </li>
                        </ul>
                        <div class="pcoded-navigatio-lavel">Management</div>
                        <ul class="pcoded-item pcoded-left-item">
                            <li>
                                <a href="{{ route('skills.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-award"></i></span>
                                    <span class="pcoded-mtext">Skills</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('previous-experiences.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-briefcase"></i></span>
                                    <span class="pcoded-mtext">Previous Experiences</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('availabilities.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-clock"></i></span>
                                    <span class="pcoded-mtext">Availabilities</span>
                                </a>
                            </li>
                        </ul>
                        <div class="pcoded-navigatio-lavel">Requests</div>
                        <ul class="pcoded-item pcoded-left-item">
                            <li>
                                <a href="{{ route('volunteer-requests.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-user-plus"></i></span>
                                    <span class="pcoded-mtext">Volunteer Requests</span>
                                </a>
                            </li>
                        </ul>
                        <div class="pcoded-navigatio-lavel">Workflows & Tasks</div>
                        <ul class="pcoded-item pcoded-left-item">
                            <li>
                                <a href="{{ route('workflows.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-shuffle"></i></span>
                                    <span class="pcoded-mtext">Workflows</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tasks.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-list"></i></span>
                                    <span class="pcoded-mtext">Tasks</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('task-events.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-activity"></i></span>
                                    <span class="pcoded-mtext">Task Events</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('progress.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-trending-up"></i></span>
                                    <span class="pcoded-mtext">تتبع التقدم</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('progress.calendar') }}">
                                    <span class="pcoded-micon"><i class="feather icon-calendar"></i></span>
                                    <span class="pcoded-mtext">تقويم المواعيد</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tasks.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-link"></i></span>
                                    <span class="pcoded-mtext">Task Dependencies</span>
                                </a>
                            </li>
                        </ul>
                        <div class="pcoded-navigatio-lavel">Projects</div>
                        <ul class="pcoded-item pcoded-left-item">
                            <li>
                                <a href="{{ route('projects.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-folder"></i></span>
                                    <span class="pcoded-mtext">Projects</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('projects.my-projects') }}">
                                    <span class="pcoded-micon"><i class="feather icon-user"></i></span>
                                    <span class="pcoded-mtext">My Projects</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('projects.team-tasks') }}">
                                    <span class="pcoded-micon"><i class="feather icon-users"></i></span>
                                    <span class="pcoded-mtext">Team Tasks</span>
                                </a>
                            </li>
                        </ul>
                        <div class="pcoded-navigatio-lavel">Analytics</div>
                        <ul class="pcoded-item pcoded-left-item">
                            <li>
                                <a href="{{ route('analytics.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-bar-chart-2"></i></span>
                                    <span class="pcoded-mtext">Task Analytics</span>
                                </a>
                            </li>
                        </ul>
                        <div class="pcoded-navigatio-lavel">Notifications</div>
                        <ul class="pcoded-item pcoded-left-item">
                            <li>
                                <a href="{{ route('notifications.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-bell"></i></span>
                                    <span class="pcoded-mtext">Notifications</span>
                                    @php
                                        $unreadCount = auth()->check() ? auth()->user()->unreadNotifications->count() : 0;
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="badge badge-danger badge-pill ml-auto">{{ $unreadCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('notifications.settings') }}">
                                    <span class="pcoded-micon"><i class="feather icon-settings"></i></span>
                                    <span class="pcoded-mtext">Notification Settings</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="pcoded-content">
                    <div class="pcoded-inner-content">