<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\ExamSchedule;
use App\Models\Score;
use App\Models\Subject;
use App\Models\User;

class ModelObserver
{
    public function created(object $model): void
    {
        $this->logAction($model, 'created');
    }

    public function updated(object $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        // Format changes for display
        $formatted = [];
        foreach ($changes as $field => $value) {
            $old = $model->getOriginal($field);
            $formatted[$field] = [
                'old' => $old,
                'new' => $value,
            ];
        }

        $this->logAction($model, 'updated', $formatted);
    }

    public function deleted(object $model): void
    {
        $this->logAction($model, 'deleted');
    }

    protected function logAction(object $model, string $action, ?array $changes = null): void
    {
        $modelType = get_class($model);

        // Only log these models
        $trackedModels = [
            User::class => User::class,
            Subject::class => Subject::class,
            ExamSchedule::class => ExamSchedule::class,
            Score::class => Score::class,
        ];

        if (!isset($trackedModels[$modelType])) {
            return;
        }

        $modelLabel = $this->getModelLabel($model);

        // Get the ID (handle both static and dynamic types)
        $modelId = $model->id ?? null;

        ActivityLog::create([
            'admin_id' => auth()->guard('admin')->id(),
            'guard_type' => 'admin',
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'model_label' => $modelLabel,
            'changes' => $changes,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'created_at' => now(),
        ]);
    }

    protected function getModelLabel(object $model): string
    {
        return match (true) {
            $model instanceof User => "User: {$model->name} ({$model->email})",
            $model instanceof Subject => "Subject: {$model->name} ({$model->code})",
            $model instanceof ExamSchedule => "Schedule: {$model->subject?->name}",
            $model instanceof Score => "Score: {$model->user?->name} - {$model->subject?->name} ({$model->score})",
            default => get_class($model) . ' #' . ($model->id ?? '?'),
        };
    }
}
