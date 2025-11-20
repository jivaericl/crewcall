<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 rounded-md">
                {{ session('message') }}
            </div>
        @endif

        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Team Roles</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage team roles for {{ $event->name }}</p>
            </div>
            <button 
                wire:click="openCreateModal" 
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 transition-colors">
                Create Role
            </button>
        </div>

        <!-- Roles List -->
        @if($roles->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Assigned Users</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sort Order</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($roles as $role)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $role->color }}"></div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $role->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $role->description ?: '—' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $role->users_count }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $role->sort_order }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button 
                                        wire:click="openAssignModal({{ $role->id }})" 
                                        class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-3">
                                        Assign Users
                                    </button>
                                    <button 
                                        wire:click="openEditModal({{ $role->id }})" 
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3">
                                        Edit
                                    </button>
                                    <button 
                                        wire:click="delete({{ $role->id }})" 
                                        wire:confirm="Are you sure you want to delete this role?"
                                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">No team roles created yet. Create your first role to get started.</p>
            </div>
        @endif

        <!-- Create/Edit Modal -->
        @if($showCreateModal)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            {{ $editingRoleId ? 'Edit Team Role' : 'Create Team Role' }}
                        </h3>

                        <form wire:submit.prevent="save">
                            <div class="space-y-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Role Name <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="name" 
                                        placeholder="e.g., Lighting Director"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                    <textarea 
                                        wire:model="description" 
                                        rows="3"
                                        placeholder="Optional description of this role"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Color -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                                    <input 
                                        type="color" 
                                        wire:model="color" 
                                        class="h-10 w-20 rounded border-gray-300 dark:border-gray-600">
                                    @error('color') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                                    <input 
                                        type="number" 
                                        wire:model="sort_order" 
                                        min="0"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('sort_order') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <button 
                                    type="button" 
                                    wire:click="closeModal" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 text-sm font-medium rounded-md bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 transition-colors">
                                    {{ $editingRoleId ? 'Update Role' : 'Create Role' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Assign Users Modal -->
        @if($showAssignModal)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Assign Users to {{ $assigningRole ? $assigningRole->name : '' }}
                        </h3>

                        <form wire:submit.prevent="saveAssignments">
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @if($availableUsers->count() > 0)
                                    @foreach($availableUsers as $user)
                                        <label class="flex items-center p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                wire:model="selectedUsers" 
                                                value="{{ $user->id }}"
                                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ $user->name }}</span>
                                        </label>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No team members available. Add users to this event first.</p>
                                @endif
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <button 
                                    type="button" 
                                    wire:click="closeAssignModal" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-4 py-2 text-sm font-medium rounded-md bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 transition-colors">
                                    Save Assignments
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
