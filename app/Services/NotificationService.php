<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;

class NotificationService
{
    /**
     * Create a notification for a user if they have the preference enabled
     */
    public static function notify(User $user, string $type, string $title, string $message, ?string $relatedModel = null, ?int $relatedId = null): ?Notification
    {
        // Get user preference
        $pref = $user->notificationPreference;
        
        // Map type to preference field
        $preferenceMap = [
            'report_created' => 'report_created',
            'report_status_changed' => 'report_status_changed',
            'user_suspended' => 'user_suspended',
            'user_activated' => 'user_activated',
            'role_changed' => 'role_changed',
        ];

        // Check if notification type is enabled for this user
        if ($pref && isset($preferenceMap[$type]) && !$pref->{$preferenceMap[$type]}) {
            return null; // Notification disabled for this user
        }

        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_model' => $relatedModel,
            'related_id' => $relatedId,
        ]);
    }

    /**
     * Notify report creator when report is created
     */
    public static function reportCreated(User $user, string $subject)
    {
        return self::notify(
            $user,
            'report_created',
            'Report Submitted',
            "Your report \"$subject\" has been successfully submitted.",
            'Report'
        );
    }

    /**
     * Notify report creator when status changes
     */
    public static function reportStatusChanged(User $user, string $subject, string $oldStatus, string $newStatus)
    {
        return self::notify(
            $user,
            'report_status_changed',
            'Report Status Updated',
            "Your report \"$subject\" status changed from $oldStatus to $newStatus.",
            'Report'
        );
    }

    /**
     * Notify user when suspended
     */
    public static function userSuspended(User $user, string $reason = null)
    {
        $message = 'Your account has been suspended.';
        if ($reason) {
            $message .= " Reason: $reason";
        }

        return self::notify(
            $user,
            'user_suspended',
            'Account Suspended',
            $message,
            'User',
            $user->id
        );
    }

    /**
     * Notify user when activated
     */
    public static function userActivated(User $user)
    {
        return self::notify(
            $user,
            'user_activated',
            'Account Reactivated',
            'Your account has been reactivated and you can now log in again.',
            'User',
            $user->id
        );
    }

    /**
     * Notify user when role changed
     */
    public static function roleChanged(User $user, string $newRole)
    {
        return self::notify(
            $user,
            'role_changed',
            'Role Changed',
            "Your role has been changed to $newRole.",
            'User',
            $user->id
        );
    }
}
