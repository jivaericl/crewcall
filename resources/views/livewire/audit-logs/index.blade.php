<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Logs</h2>
            @if($event)
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Activity log for {{ $event->name }}
                </p>
            @else
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    System-wide activity log
                </p>
            @endif
        </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Filters Section -->
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Search -->
                        <div>
                            <flux:input
                                wire:model.live.debounce.300ms="search"
                                type="text"
                                placeholder="Search..."
                                class="w-full"
                            />
                        </div>

                        <!-- Event Filter -->
                        <div>
                            <flux:select wire:model.live="filterEvent">
                                <option value="">All Events</option>
                                <option value="created">Created</option>
                                <option value="updated">Updated</option>
                                <option value="deleted">Deleted</option>
                                <option value="restored">Restored</option>
                            </flux:select>
                        </div>

                        <!-- Model Filter -->
                        <div>
                            <flux:select wire:model.live="filterModel">
                                <option value="">All Models</option>
                                @foreach($modelTypes as $modelType)
                                    <option value="{{ $modelType['value'] }}">{{ $modelType['label'] }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <!-- User Filter -->
                        <div>
                            <flux:select wire:model.live="filterUser">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    </div>

                    <!-- Audit Logs Table -->
                    @if($auditLogs->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Event
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Model
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Description
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            When
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($auditLogs as $log)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $eventColors = [
                                                        'created' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                        'updated' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                        'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                        'restored' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                                    ];
                                                    $colorClass = $eventColors[$log->event] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                                    {{ ucfirst($log->event) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ class_basename($log->auditable_type) }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    ID: {{ $log->auditable_id }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $log->user ? $log->user->name : 'System' }}
                                                </div>
                                                @if($log->ip_address)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $log->ip_address }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ $log->description }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <div>{{ $log->created_at->format('M d, Y') }}</div>
                                                <div>{{ $log->created_at->format('H:i:s') }}</div>
                                                <div class="text-xs">{{ $log->created_at->diffForHumans() }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end gap-2">
                                                    @if($log->record_url)
                                                        <flux:button
                                                            href="{{ $log->record_url }}"
                                                            wire:navigate
                                                            variant="ghost"
                                                            size="sm"
                                                            title="Visit"
                                                        >
                                                            <x-action-icon action="go-to" />
                                                        </flux:button>
                                                    @endif
                                                    <flux:button
                                                        wire:click="showDetails({{ $log->id }})"
                                                        variant="ghost"
                                                        size="sm"
                                                        title="View"
                                                    >
                                                        <x-action-icon action="view" />
                                                    </flux:button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $auditLogs->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400 text-lg">No audit logs found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <div x-data="{ show: @entangle('showDetailsModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>

            @if($selectedLog)
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-3xl w-full p-6 max-h-[90vh] overflow-y-auto">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Audit Log Details
                    </h3>

                    <div class="space-y-4">
                        <!-- Event Info -->
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Event</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($selectedLog->event) }}</p>
                        </div>

                        <!-- Model Info -->
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Model</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ class_basename($selectedLog->auditable_type) }} #{{ $selectedLog->auditable_id }}
                            </p>
                        </div>

                        <!-- User Info -->
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">User</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $selectedLog->user ? $selectedLog->user->name : 'System' }}
                                @if($selectedLog->user)
                                    <span class="text-gray-500 dark:text-gray-400">({{ $selectedLog->user->email }})</span>
                                @endif
                            </p>
                        </div>

                        <!-- Timestamp -->
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">When</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                {{ $selectedLog->created_at->format('F d, Y \a\t H:i:s') }}
                                <span class="text-gray-500 dark:text-gray-400">({{ $selectedLog->created_at->diffForHumans() }})</span>
                            </p>
                        </div>

                        <!-- IP Address -->
                        @if($selectedLog->ip_address)
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">IP Address</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $selectedLog->ip_address }}</p>
                            </div>
                        @endif

                        <!-- User Agent -->
                        @if($selectedLog->user_agent)
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">User Agent</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100 break-all">{{ $selectedLog->user_agent }}</p>
                            </div>
                        @endif

                        <!-- Changes -->
                        @if($selectedLog->changes)
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block">Changes</label>
                                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                                    @if($selectedLog->event === 'created')
                                        @foreach($selectedLog->changes as $field => $value)
                                            <div class="border-b border-gray-200 dark:border-gray-700 pb-2 last:border-0">
                                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $field }}</div>
                                                <div class="mt-1 text-sm text-green-600 dark:text-green-400">
                                                    {{ is_array($value) ? json_encode($value) : $value }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @elseif($selectedLog->event === 'deleted')
                                        @foreach($selectedLog->changes as $field => $value)
                                            <div class="border-b border-gray-200 dark:border-gray-700 pb-2 last:border-0">
                                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $field }}</div>
                                                <div class="mt-1 text-sm text-red-600 dark:text-red-400">
                                                    {{ is_array($value) ? json_encode($value) : $value }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        @foreach($selectedLog->changes as $field => $change)
                                            <div class="border-b border-gray-200 dark:border-gray-700 pb-2 last:border-0">
                                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ $field }}</div>
                                                <div class="mt-1 space-y-1">
                                                    <div class="text-sm">
                                                        <span class="text-red-600 dark:text-red-400">Old:</span>
                                                        <span class="text-gray-900 dark:text-gray-100">
                                                            {{ is_array($change['old']) ? json_encode($change['old']) : ($change['old'] ?? 'null') }}
                                                        </span>
                                                    </div>
                                                    <div class="text-sm">
                                                        <span class="text-green-600 dark:text-green-400">New:</span>
                                                        <span class="text-gray-900 dark:text-gray-100">
                                                            {{ is_array($change['new']) ? json_encode($change['new']) : ($change['new'] ?? 'null') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end mt-6">
                        <button wire:click="closeDetails" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                            Close
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
