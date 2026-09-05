<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastReceipt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'broadcast_id',
        'user_id',
        'dismissed',
        'dismissed_at',
        'read_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'dismissed' => 'boolean',
            'dismissed_at' => 'datetime',
            'read_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function broadcast()
    {
        return $this->belongsTo(Broadcast::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function dismiss(): void
    {
        if (!$this->dismissed) {
            $this->update(['dismissed' => true, 'dismissed_at' => now()]);
        }
    }
}
