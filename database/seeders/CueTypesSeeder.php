<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CueTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * System-wide cue types for production management.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $cueTypes = [
            [
                'name' => 'Audio',
                'slug' => 'audio',
                'color' => '#3b82f6', // Blue
                'icon' => 'volume-up',
                'is_system' => true,
                'is_active' => true,
                'event_id' => null, // System-wide
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Lights',
                'slug' => 'lights',
                'color' => '#eab308', // Yellow
                'icon' => 'lightbulb',
                'is_system' => true,
                'is_active' => true,
                'event_id' => null,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Playback',
                'slug' => 'playback',
                'color' => '#8b5cf6', // Purple
                'icon' => 'play-circle',
                'is_system' => true,
                'is_active' => true,
                'event_id' => null,
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Graphics',
                'slug' => 'graphics',
                'color' => '#06b6d4', // Cyan
                'icon' => 'photo',
                'is_system' => true,
                'is_active' => true,
                'event_id' => null,
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Switching',
                'slug' => 'switching',
                'color' => '#10b981', // Green
                'icon' => 'switch-horizontal',
                'is_system' => true,
                'is_active' => true,
                'event_id' => null,
                'sort_order' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Stage Hand',
                'slug' => 'stage-hand',
                'color' => '#f59e0b', // Amber
                'icon' => 'hand',
                'is_system' => true,
                'is_active' => true,
                'event_id' => null,
                'sort_order' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Check if cue types already exist to avoid duplicates
        foreach ($cueTypes as $cueType) {
            DB::table('cue_types')->updateOrInsert(
                ['slug' => $cueType['slug']],
                $cueType
            );
        }

        $this->command->info('✓ Created 6 system-wide cue types');
        $this->command->info('  - Audio (blue)');
        $this->command->info('  - Lights (yellow)');
        $this->command->info('  - Playback (purple)');
        $this->command->info('  - Graphics (cyan)');
        $this->command->info('  - Switching (green)');
        $this->command->info('  - Stage Hand (amber)');
    }
}
