<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSchedule extends Model
{
    protected $fillable = [
        'subject_id',
        'department_id',
        'briefing_datetime',
        'exam_start_datetime',
        'exam_end_datetime',
        'exam_link',
        'exam_password',
        'exam_number',
        'is_published',
        'link_reveal',
        'password_reveal',
    ];

    protected function casts(): array
    {
        return [
            'briefing_datetime' => 'datetime',
            'exam_start_datetime' => 'datetime',
            'exam_end_datetime' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function isLinkVisible(): bool
    {
        $now = Carbon::now();

        return match ($this->link_reveal) {
            'on_briefing' => $now->gte($this->briefing_datetime),
            'on_start' => $now->gte($this->exam_start_datetime),
            'always' => true,
            default => false,
        };
    }

    public function isPasswordVisible(): bool
    {
        $now = Carbon::now();

        return match ($this->password_reveal) {
            'on_briefing' => $now->gte($this->briefing_datetime),
            '5_min_before' => $now->gte($this->exam_start_datetime->copy()->subMinutes(5)),
            'on_start' => $now->gte($this->exam_start_datetime),
            'always' => true,
            default => false,
        };
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}