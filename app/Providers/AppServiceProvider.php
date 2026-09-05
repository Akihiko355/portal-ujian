<?php

namespace App\Providers;

use App\Models\ExamSchedule;
use App\Models\Score;
use App\Models\Subject;
use App\Models\User;
use App\Observers\ModelObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        User::observe(ModelObserver::class);
        Subject::observe(ModelObserver::class);
        ExamSchedule::observe(ModelObserver::class);
        Score::observe(ModelObserver::class);
    }
}
