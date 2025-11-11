<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Cues - {{ $segment->name }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $segment->session->name }} | {{ $segment->session->event->name }}
            </p>
        </div>

        <div>
            @if (session()->has('message'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('message') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Header Actions -->
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex gap-2">
                            <flux:button href="{{ route('sessions.segments.index', $segment->session_id) }}" variant="ghost">
                                ← Back to Segments
                            </flux:button>
                        </div>
                        <div class="flex gap-2">
                            <button 
                                wire:click="openResetModal" 
                                class="px-4 py-2 bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white text-sm font-medium rounded-md"
                                type="button">
                                Reset All Cues
                            </button>
                            <flux:button href="{{ route('segments.cues.create', $segmentId) }}" variant="primary">
                                Add Cue
                            </flux:button>
                        </div>
                    </div>

                    <!-- Search and Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                        <div class="md:col-span-2">
                            <flux:input 
                                wire:model.live.debounce.300ms="search" 
                                type="text" 
                                placeholder="Search cues..." 
                                class="w-full"
                            />
                        </div>
                        <div>
                            <flux:select wire:model.live="cueTypeFilter" class="w-full">
                                <option value="">All Types</option>
                                @foreach($cueTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                        <div>
                            <flux:select wire:model.live="statusFilter" class="w-full">
                                <option value="">All Statuses</option>
                                <option value="standby">Standby</option>
                                <option value="go">Go</option>
                                <option value="complete">Complete</option>
                                <option value="skip">Skip</option>
                            </flux:select>
                        </div>
                        <div>
                            <flux:select wire:model.live="priorityFilter" class="w-full">
                                <option value="">All Priorities</option>
                                <option value="low">Low</option>
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </flux:select>
                        </div>
                    </div>

                    <!-- Cues Table -->
                    @if($cues->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cue</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Priority</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Operator</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($cues as $cue)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 align-top text-sm text-gray-900 dark:text-gray-100">
                                                <div>{{ $cue->time ? $cue->time->format('g:i A') : '-' }}</div>
                                                @if($cue->code)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $cue->code }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 align-top">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cue->name }}</div>
                                                @if($cue->filename)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        📁 {{ $cue->filename }}
                                                    </div>
                                                @endif
                                                @if($cue->tags->count() > 0)
                                                    <div class="flex flex-wrap gap-1 mt-2">
                                                        @foreach($cue->tags as $tag)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}">
                                                                {{ $tag->name }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 align-top whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $cue->cueType->color }}20; color: {{ $cue->cueType->color }}">
                                                    {{ $cue->cueType->name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 align-top whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    @if($cue->priority === 'critical') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                    @elseif($cue->priority === 'high') bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200
                                                    @elseif($cue->priority === 'normal') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                                    @endif">
                                                    {{ ucfirst($cue->priority) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 align-top whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $cue->operator?->name ?: '-' }}
                                            </td>
                                            <td class="px-6 py-4 align-top whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end gap-1">
                                                    <a href="{{ route('segments.cues.show', [$segmentId, $cue->id]) }}" class="inline-flex items-center px-2 py-1 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors" title="View">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                    <flux:button href="{{ route('segments.cues.edit', [$segmentId, $cue->id]) }}" variant="ghost" size="sm" title="Edit">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </flux:button>
                                                    <flux:button wire:click="duplicateCue({{ $cue->id }})" variant="ghost" size="sm" title="Duplicate">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </flux:button>
                                                    <flux:button wire:click="confirmDelete({{ $cue->id }})" variant="danger" size="sm" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </flux:button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $cues->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No cues found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new cue.</p>
                            <div class="mt-6">
                                <flux:button href="{{ route('segments.cues.create', $segmentId) }}" variant="primary">
                                    Add Cue
                                </flux:button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($deleteId)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600 dark:text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Delete Cue</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete this cue? This action can be undone from the audit log.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <flux:button wire:click="deleteCue" variant="danger" class="ml-3">Delete</flux:button>
                        <flux:button wire:click="$set('deleteId', null)" variant="ghost">Cancel</flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Reset Confirmation Modal -->
    <div x-data="{ show: @entangle('showResetModal') }" 
         x-show="show" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" 
             @click="show = false"></div>
        
        <!-- Modal -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6"
                 @click.stop>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Reset All Cues
                </h3>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    This action will set all cues in this segment back to the default state. 
                    To confirm, type <strong>RESET</strong> in the box below.
                </p>
                
                @if(session('error'))
                    <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-800 text-red-700 dark:text-red-400 rounded text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                
                <input 
                    type="text" 
                    wire:model="resetConfirmation" 
                    placeholder="Type RESET to confirm"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white mb-4"
                />
                
                <div class="flex gap-2 justify-end">
                    <button 
                        wire:click="closeResetModal" 
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-md text-sm font-medium"
                        type="button">
                        Cancel
                    </button>
                    <button 
                        wire:click="resetAllCues" 
                        class="px-4 py-2 bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white rounded-md text-sm font-medium"
                        type="button">
                        Reset All Cues
                    </button>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
