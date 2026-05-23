<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_created',
        'report_status_changed',
        'user_suspended',
        'user_activated',
        'role_changed',
    ];

    protected $casts = [
        'report_created' => 'boolean',
        'report_status_changed' => 'boolean',
        'user_suspended' => 'boolean',
        'user_activated' => 'boolean',
        'role_changed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
