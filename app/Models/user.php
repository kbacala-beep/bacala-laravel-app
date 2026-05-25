<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Report;
use App\Models\Barangay;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'role',              // Added: to support the 'resident' string field
        'barangay_id',       // Added: to link user to their barangay
        'profile_photo',
        'phone',
        'address',
        'is_suspended',
        'suspended_at',
        'suspension_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_suspended' => 'boolean',
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * Role relationship with a default fallback.
     */
    public function role_relation()
    {
        // Renamed to avoid conflict with the 'role' string column
        return $this->belongsTo(Role::class, 'role_id')->withDefault([
            'name' => 'Resident',
        ]);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationPreference()
    {
        return $this->hasOne(NotificationPreference::class);
    }

    public function isSuspended(): bool
    {
        return (bool) $this->is_suspended;
    }

    public function isAdmin(): bool
    {
        return strtolower($this->role_relation->name ?? 'resident') === 'admin';
    }
}