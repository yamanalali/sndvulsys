<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\TaskAssigned;
use App\Events\TaskStatusChanged;
use App\Events\TaskCompleted;
use App\Events\TaskOverdue;
use App\Events\TaskDeadlineApproaching;
use App\Listeners\SendTaskAssignmentNotification;
use App\Listeners\SendTaskStatusChangeNotification;
use App\Listeners\SendTaskCompletionNotification;
use App\Listeners\SendTaskOverdueNotification;
use App\Listeners\SendTaskDeadlineReminderNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register notification event listeners
        Event::listen(TaskAssigned::class, SendTaskAssignmentNotification::class);
        Event::listen(TaskStatusChanged::class, SendTaskStatusChangeNotification::class);
        Event::listen(TaskCompleted::class, SendTaskCompletionNotification::class);
        Event::listen(TaskOverdue::class, SendTaskOverdueNotification::class);
        Event::listen(TaskDeadlineApproaching::class, SendTaskDeadlineReminderNotification::class);
    }
}
