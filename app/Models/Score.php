<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
        'exam_schedule_id',
        'score',
        'is_published',
        'published_at',
    ];

    protected $guarded = ['input_by_admin_id'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function examSchedule()
    {
        return $this->belongsTo(ExamSchedule::class);
    }

    public function inputByAdmin()
    {
        return $this->belongsTo(Admin::class, 'input_by_admin_id');
    }

    public function isPassed()
    {
        return $this->score >= $this->subject->passing_grade;
    }
}