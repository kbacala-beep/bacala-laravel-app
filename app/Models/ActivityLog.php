<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'meta',
        'ip_address',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Static helper — call this anywhere to record an action
    |
    | Usage:
    |   ActivityLog::record('status_updated', $report, 'Status changed to Resolved', [
    |       'old_status' => 'Pending',
    |       'new_status' => 'Resolved',
    |   ]);
    |--------------------------------------------------------------------------
    */
    public static function record(
        string $action,
        Model  $entity,
        string $description,
        array  $meta = []
    ): void {
        try {
            static::create([
                'user_id'     => Auth::id(),
                'action'      => $action,
                'entity_type' => class_basename($entity),
                'entity_id'   => $entity->getKey(),
                'description' => $description,
                'meta'        => $meta ?: null,
                'ip_address'  => Request::ip(),
            ]);
        } catch (\Throwable $e) {
            // Never let logging crash the application
            \Log::warning('ActivityLog::record failed: ' . $e->getMessage());
        }
    }
}
