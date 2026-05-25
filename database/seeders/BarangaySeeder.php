<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barangay;
use Illuminate\Support\Facades\DB;

class BarangaySeeder extends Seeder
{
    public function run(): void
    {
        // Optional: Clear existing data to avoid duplicates during testing
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Barangay::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $barangays = [
            ['name' => 'San Juan', 'city_municipality' => 'Surigao City'],
            ['name' => 'Canlanipa', 'city_municipality' => 'Surigao City'],
            ['name' => 'Washington', 'city_municipality' => 'Surigao City'],
            ['name' => 'Taft', 'city_municipality' => 'Surigao City'],
            ['name' => 'Sabang', 'city_municipality' => 'Surigao City'],
        ];

        foreach ($barangays as $barangay) {
            Barangay::create($barangay);
            $this->command->info("Created barangay: {$barangay['name']}");
        }
    }
}   