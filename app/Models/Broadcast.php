<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'title',
        'content',
        'urgency',
        'target_type',
        'target_ids',
        'send_at',
        'expires_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'target_ids' => 'array',
            'send_at' => 'datetime',
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function receipts()
    {
        return $this->hasMany(BroadcastReceipt::class);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeSent($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('send_at')
              ->orWhere('send_at', '<=', now());
        });
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('target_type', 'all')
              ->orWhere(function ($q2) use ($user) {
                  $q2->where('target_type', 'department')
                     ->whereJsonContains('target_ids', $user->department_id);
              });
        });
    }

    public function getDeliveryCountAttribute(): int
    {
        return $this->receipts()->count();
    }

    public function getReadCountAttribute(): int
    {
        return $this->receipts()->whereNotNull('read_at')->count();
    }

    public function getReadRateAttribute(): float
    {
        $total = $this->delivery_count;
        return $total > 0 ? round($this->read_count / $total * 100, 1) : 0;
    }
}
