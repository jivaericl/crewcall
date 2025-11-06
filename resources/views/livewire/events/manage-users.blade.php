<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manage Event Users') }}: {{ $event->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
            <button wire:click="openAddModal" type="button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                Assign User
            </button>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    @if($assignments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Admin</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($assignments as $assignment)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $assignment->user_name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $assignment->user_email }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $assignment->role_name }}</td>
                                            <td class="px-6 py-4">
                                                @if($assignment->is_admin)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Event Admin</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <flux:button wire:click="toggleAdmin({{ $assignment->id }})" variant="ghost" size="sm">
                                                    {{ $assignment->is_admin ? 'Remove Admin' : 'Make Admin' }}
                                                </flux:button>
                                                <flux:button wire:click="removeAssignment({{ $assignment->id }})" variant="ghost" size="sm">Remove</flux:button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400 text-lg mb-4">No users assigned yet.</p>
                            <button wire:click="openAddModal" type="button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                Assign First User
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Modal -->
    <div x-data="{ show: @entangle('showAddModal') }" 
         x-show="show" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         @keydown.escape.window="show = false">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
             @click="show = false"></div>
        
        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6"
                 @click.stop>
                
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Assign User to Event</h3>
                
                <!-- DEBUG -->
                <div class="mb-4 p-2 bg-yellow-100 dark:bg-yellow-900 text-xs rounded">
                    <div>selectedUserId: {{ $selectedUserId ?? 'null' }}</div>
                    <div>selectedRoleId: {{ $selectedRoleId ?? 'null' }}</div>
                    <div>isAdmin: {{ $isAdmin ? 'true' : 'false' }}</div>
                </div>
                
                <div>
                    <div class="mb-4">
                        <flux:field>
                            <flux:label>User *</flux:label>
                            <flux:select wire:model.live="selectedUserId" required>
                                <option value="">Select a user</option>
                                @foreach($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </flux:select>
                            @error('selectedUserId') <flux:error>{{ $message }}</flux:error> @enderror
                        </flux:field>
                    </div>
                    <div class="mb-4">
                        <flux:field>
                            <flux:label>Role *</flux:label>
                            <flux:select wire:model.live="selectedRoleId" required>
                                <option value="">Select a role</option>
                                @foreach($activeRoles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </flux:select>
                            @error('selectedRoleId') <flux:error>{{ $message }}</flux:error> @enderror
                        </flux:field>
                    </div>
                    <div class="mb-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model.live="isAdmin" class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Make Event Admin</span>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button wire:click="closeAddModal" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button wire:click="assignUser" type="button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700" onclick="console.log('Button clicked!'); alert('Button clicked - check console');">
                            Assign User
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
