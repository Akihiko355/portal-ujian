<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['name', 'code', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'department_subject');
    }

    public function examSchedules(): HasMany
    {
        return $this->hasMany(ExamSchedule::class);
    }
}