<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CueType;
use Illuminate\Support\Str;

class CueTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cueTypes = [
            ['name' => 'Lighting', 'color' => '#FBBF24', 'sort_order' => 1],
            ['name' => 'Audio', 'color' => '#10B981', 'sort_order' => 2],
            ['name' => 'Video', 'color' => '#3B82F6', 'sort_order' => 3],
            ['name' => 'Presentation', 'color' => '#8B5CF6', 'sort_order' => 4],
            ['name' => 'Furniture', 'color' => '#6B7280', 'sort_order' => 5],
            ['name' => 'Speaker Clock', 'color' => '#EF4444', 'sort_order' => 6],
            ['name' => 'Downstage Monitors', 'color' => '#06B6D4', 'sort_order' => 7],
        ];

        foreach ($cueTypes as $type) {
            CueType::create([
                'name' => $type['name'],
                'slug' => Str::slug($type['name']),
                'color' => $type['color'],
                'is_system' => true,
                'is_active' => true,
                'event_id' => null, // System-wide
                'sort_order' => $type['sort_order'],
            ]);
        }
    }
}
