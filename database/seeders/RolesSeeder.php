<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Full event administrator with all permissions',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Producer',
                'slug' => 'producer',
                'description' => 'Content producer with editing rights',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Content Producer',
                'slug' => 'content-producer',
                'description' => 'Content producer focused on media files',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Client',
                'slug' => 'client',
                'description' => 'Client with limited viewing access',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Read-only access to event',
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Check if roles already exist
        $existingRoles = DB::table('roles')->count();
        
        if ($existingRoles === 0) {
            DB::table('roles')->insert($roles);
            $this->command->info('✓ Seeded ' . count($roles) . ' roles successfully.');
        } else {
            $this->command->warn('⚠ Roles already exist. Skipping seeding.');
        }
    }
}
