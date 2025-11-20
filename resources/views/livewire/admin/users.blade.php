<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">User Management</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage all user accounts</p>
            </div>
            <flux:button href="{{ route('admin.dashboard') }}" variant="ghost">
                Back to Dashboard
            </flux:button>
        </div>

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

        <!-- Search -->
        <div class="mb-6">
            <flux:input 
                wire:model.live.debounce.300ms="search" 
                type="text"
                placeholder="Search users by name or email..."
                class="w-full"
            />
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Events</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                            <span class="text-lg font-medium text-gray-600 dark:text-gray-300">
                                                {{ substr($user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->assigned_events_count }} events
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_super_admin)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Super Admin
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        User
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button 
                                    wire:click="toggleSuperAdmin({{ $user->id }})" 
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3"
                                    @if($user->id === auth()->id()) disabled title="Cannot modify your own status" @endif>
                                    {{ $user->is_super_admin ? 'Remove Admin' : 'Make Admin' }}
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $user->id }})" 
                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                    @if($user->id === auth()->id()) disabled title="Cannot delete yourself" @endif>
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $users->links() }}
        </div>

        <!-- Delete Confirmation Modal -->
        @if($showDeleteModal && $userToDelete)
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Confirm Delete
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                            Are you sure you want to delete <strong>{{ $userToDelete->name }}</strong>? This action cannot be undone.
                        </p>
                        <div class="flex justify-end gap-3">
                            <button 
                                type="button" 
                                wire:click="closeModal" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                                Cancel
                            </button>
                            <button 
                                type="button" 
                                wire:click="deleteUser" 
                                class="px-4 py-2 text-sm font-medium rounded-md bg-red-600 text-white hover:bg-red-700">
                                Delete User
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
