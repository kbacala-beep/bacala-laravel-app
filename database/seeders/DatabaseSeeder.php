<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Order matters:
     * 1. Roles must exist before users.
     * 2. Barangays must exist before users/admins.
     * 3. AdminSeeder depends on both Roles and Barangays.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,     //
            BarangaySeeder::class, // Added: Necessary for users/admins
            AdminSeeder::class,    //
            CategorySeeder::class, //
        ]);
    }
}