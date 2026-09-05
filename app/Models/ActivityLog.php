<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'admin_id',
        'guard_type',
        'action',
        'model_type',
        'model_id',
        'model_label',
        'changes',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'model_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'model_id');
    }

    public function scopeForAdmin($query)
    {
        return $query->where('guard_type', 'admin');
    }

    public function scopeForStudent($query)
    {
        return $query->where('guard_type', 'student');
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByModel($query, string $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public static function log(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        ?string $modelLabel = null,
        ?array $changes = null,
        ?array $metadata = null,
        ?string $guardType = 'admin'
    ): self {
        $request = request();

        return static::create([
            'admin_id' => auth()->guard('admin')->id(),
            'guard_type' => $guardType,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'model_label' => $modelLabel,
            'changes' => $changes,
            'metadata' => $metadata,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ]);
    }

    public function getIconAttribute(): string
    {
        return match ($this->action) {
            'created' => '➕',
            'updated' => '✏️',
            'deleted' => '🗑️',
            'login' => '🔓',
            'logout' => '🔒',
            'published' => '✅',
            'unpublished' => '⏸️',
            'imported' => '📥',
            'exported' => '📤',
            'broadcast_sent' => '📢',
            'bulk_action' => '⚡',
            default => '📋',
        };
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'created' => 'Membuat',
            'updated' => 'Mengubah',
            'deleted' => 'Menghapus',
            'login' => 'Login',
            'logout' => 'Logout',
            'published' => 'Mempublikasi',
            'unpublished' => 'Membatalkan Publikasi',
            'imported' => 'Mengimport',
            'exported' => 'Meng-export',
            'broadcast_sent' => 'Mengirim Broadcast',
            'bulk_action' => 'Aksi Massal',
            default => ucfirst($this->action),
        };
    }
}
