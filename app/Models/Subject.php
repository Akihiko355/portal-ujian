<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = ['name', 'code', 'credits', 'description', 'passing_grade'];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_subject');
    }

    public function examSchedules(): HasMany
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}