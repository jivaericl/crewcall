<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Executive Sponsor',
                'description' => 'High-level executive sponsor for the event',
                'can_view' => true,
                'can_edit' => false,
                'can_add' => false,
                'can_delete' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Executive Producer',
                'description' => 'Executive producer with full control',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => true,
                'can_delete' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Content Producer',
                'description' => 'Manages content for the event',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => true,
                'can_delete' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Media Producer',
                'description' => 'Manages media assets and production',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => true,
                'can_delete' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Technical Director',
                'description' => 'Oversees technical aspects of the event',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => true,
                'can_delete' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'Graphic Designer',
                'description' => 'Creates and manages graphic elements',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => true,
                'can_delete' => false,
                'sort_order' => 6,
            ],
            [
                'name' => 'Art Director',
                'description' => 'Directs artistic vision and design',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => true,
                'can_delete' => false,
                'sort_order' => 7,
            ],
            [
                'name' => 'Audio [A1]',
                'description' => 'Primary audio engineer',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => true,
                'can_delete' => false,
                'sort_order' => 8,
            ],
            [
                'name' => 'Audio [A2]',
                'description' => 'Secondary audio engineer',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => false,
                'can_delete' => false,
                'sort_order' => 9,
            ],
            [
                'name' => 'Lighting Director',
                'description' => 'Manages lighting design and operation',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => true,
                'can_delete' => false,
                'sort_order' => 10,
            ],
            [
                'name' => 'Stage Hand',
                'description' => 'Assists with stage setup and operations',
                'can_view' => true,
                'can_edit' => false,
                'can_add' => false,
                'can_delete' => false,
                'sort_order' => 11,
            ],
            [
                'name' => 'Assistant Stage Manager',
                'description' => 'Assists the stage manager',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => false,
                'can_delete' => false,
                'sort_order' => 12,
            ],
            [
                'name' => 'Stage Manager',
                'description' => 'Manages stage operations and coordination',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => true,
                'can_delete' => false,
                'sort_order' => 13,
            ],
            [
                'name' => 'Show Caller',
                'description' => 'Calls cues during the show',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => false,
                'can_delete' => false,
                'sort_order' => 14,
            ],
            [
                'name' => 'Client',
                'description' => 'Client representative with view access',
                'can_view' => true,
                'can_edit' => false,
                'can_add' => false,
                'can_delete' => false,
                'sort_order' => 15,
            ],
            [
                'name' => 'Projection/LED',
                'description' => 'Manages projection and LED displays',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => true,
                'can_delete' => false,
                'sort_order' => 16,
            ],
            [
                'name' => 'Playback',
                'description' => 'Manages media playback systems',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => false,
                'can_delete' => false,
                'sort_order' => 17,
            ],
            [
                'name' => 'Graphic Operator',
                'description' => 'Operates graphics systems during the event',
                'can_view' => true,
                'can_edit' => true,
                'can_add' => false,
                'can_delete' => false,
                'sort_order' => 18,
            ],
            [
                'name' => 'Production Assistant [PA]',
                'description' => 'Provides general production assistance',
                'can_view' => true,
                'can_edit' => false,
                'can_add' => false,
                'can_delete' => false,
                'sort_order' => 19,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::create(array_merge($roleData, [
                'is_system' => true,
                'is_active' => true,
            ]));
        }
    }
}
