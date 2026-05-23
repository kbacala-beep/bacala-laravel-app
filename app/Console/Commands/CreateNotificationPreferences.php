<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\NotificationPreference;

class CreateNotificationPreferences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:init-preferences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default notification preferences for users who don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;

        User::whereDoesntHave('notificationPreference')->each(function ($user) use (&$count) {
            NotificationPreference::create([
                'user_id' => $user->id,
                'report_created' => true,
                'report_status_changed' => true,
                'user_suspended' => true,
                'user_activated' => true,
                'role_changed' => true,
            ]);
            $count++;
        });

        $this->info("✓ Created notification preferences for {$count} users.");
        return 0;
    }
}
