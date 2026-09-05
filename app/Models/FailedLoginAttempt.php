<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedLoginAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'email',
        'ip_address',
        'attempted_at',
        'guard_type',
    ];

    protected function casts(): array
    {
        return [
            'attempted_at' => 'datetime',
        ];
    }
}