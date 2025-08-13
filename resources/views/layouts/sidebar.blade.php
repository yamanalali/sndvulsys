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
                                    <span class="pcoded-mtext">Availability</span>
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
                            <li>
                                <a href="{{ route('volunteer-evaluations.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-star"></i></span>
                                    <span class="pcoded-mtext">Volunteer Evaluations</span>
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
                                <a href="{{ route('tasks.dependencies.form', ['id' => 1]) }}">
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
                        <div class="pcoded-navigatio-lavel">Documents</div>
                        <ul class="pcoded-item pcoded-left-item">
                            <li>
                                <a href="{{ route('documents.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-file-text"></i></span>
                                    <span class="pcoded-mtext">Documents</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('documents.create') }}">
                                    <span class="pcoded-micon"><i class="feather icon-upload"></i></span>
                                    <span class="pcoded-mtext">Upload Document</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('documents.all-backups') }}">
                                    <span class="pcoded-micon"><i class="feather icon-save"></i></span>
                                    <span class="pcoded-mtext">Document Backups</span>
                                </a>
                            </li>
                        </ul>
                        <div class="pcoded-navigatio-lavel">Case Management</div>
                        <ul class="pcoded-item pcoded-left-item">
                            <li>
                                <a href="{{ route('case-management.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-briefcase"></i></span>
                                    <span class="pcoded-mtext">Case Management</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('case-management.dashboard') }}">
                                    <span class="pcoded-micon"><i class="feather icon-bar-chart-2"></i></span>
                                    <span class="pcoded-mtext">Case Dashboard</span>
                                </a>
                            </li>
                        </ul>
                        <div class="pcoded-navigatio-lavel">Submissions</div>
                        <ul class="pcoded-item pcoded-left-item">
                            <li>
                                <a href="{{ route('submissions.index') }}">
                                    <span class="pcoded-micon"><i class="feather icon-send"></i></span>
                                    <span class="pcoded-mtext">Submissions</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('submissions.create') }}">
                                    <span class="pcoded-micon"><i class="feather icon-plus"></i></span>
                                    <span class="pcoded-mtext">Create Submission</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="pcoded-content">
                    <div class="pcoded-inner-content">