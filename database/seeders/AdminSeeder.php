<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Barangay;

class AdminSeeder extends Seeder
{
    /**
     * Seeds the default barangay admin account.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        
        // We need at least one barangay to exist to attach the admin to it
        $defaultBarangay = Barangay::first();

        if (!$adminRole) {
            $this->command->error('Admin role not found. Run RoleSeeder first.');
            return;
        }

        if (!$defaultBarangay) {
            $this->command->error('No Barangay found. Run BarangaySeeder first.');
            return;
        }

        // Check if an admin already exists — don't create duplicates
        $existingAdmin = User::where('email', 'admin@brgycirs.local')->first();

        if ($existingAdmin) {
            $this->command->warn("An admin account already exists: {$existingAdmin->email}. Skipping.");
            return;
        }

        $admin = User::create([
            'name'        => 'Barangay Admin',
            'email'       => 'admin@brgycirs.local',
            'password'    => Hash::make('Bacala01'), // Default password, should be changed after first login
            'role_id'     => $adminRole->id,
            'barangay_id' => $defaultBarangay->id, // Added: Necessary for scoped access
            'role'        => 'admin',              // Added: Matches your User::isAdmin() check
        ]);

        $this->command->info("Admin account created:");
        $this->command->info("  Email    : {$admin->email}");
        $this->command->info("  Barangay : {$defaultBarangay->name}");
        $this->command->info("  Password : Bacala01");
        $this->command->warn("  !! Change this password immediately after first login !!");
    }
}