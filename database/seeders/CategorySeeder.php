<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Noise Complaint',    'color' => '#F57F17', 'icon' => 'fa-volume-up'],
            ['name' => 'Property Damage',    'color' => '#C62828', 'icon' => 'fa-hammer'],
            ['name' => 'Public Safety',      'color' => '#B71C1C', 'icon' => 'fa-shield-alt'],
            ['name' => 'Sanitation',         'color' => '#2E7D32', 'icon' => 'fa-trash'],
            ['name' => 'Illegal Parking',    'color' => '#1565C0', 'icon' => 'fa-car'],
            ['name' => 'Streetlight Issue',  'color' => '#F9A825', 'icon' => 'fa-lightbulb'],
            ['name' => 'Road Damage',        'color' => '#4E342E', 'icon' => 'fa-road'],
            ['name' => 'Flooding',           'color' => '#0277BD', 'icon' => 'fa-water'],
            ['name' => 'Stray Animals',      'color' => '#6A1B9A', 'icon' => 'fa-paw'],
            ['name' => 'Other',              'color' => '#6E6E73', 'icon' => 'fa-tag'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
