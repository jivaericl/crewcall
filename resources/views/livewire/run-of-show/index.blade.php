<div wire:poll.2s="refreshActiveSegment">
    <flux:header container class="mb-6">
        <div>
            <flux:heading size="xl">Run of Show</flux:heading>
            <flux:subheading>{{ $session->name }}</flux:subheading>
        </div>
        
        <div class="flex gap-2">
            @if($activeSegmentId)
                <flux:button wire:click="clearActiveSegment" variant="ghost"><x-action-icon action="x-mark" />Clear Active</flux:button>
            @endif
            
            <flux:button wire:click="openColumnModal"><x-action-icon action="view-columns" />Columns</flux:button>
            
            <flux:button href="{{ route('sessions.segments.index', $sessionId) }}" variant="ghost"><x-action-icon action="edit" />Edit Segments</flux:button>
        </div>
    </flux:header>

    @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        @if($this->isColumnVisible('order'))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                #
                            </th>
                        @endif
                        
                        @if($this->isColumnVisible('name'))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Name
                            </th>
                        @endif
                        
                        @if($this->isColumnVisible('start_time'))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Start Time
                            </th>
                        @endif
                        
                        @if($this->isColumnVisible('end_time'))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                End Time
                            </th>
                        @endif
                        
                        @if($this->isColumnVisible('duration'))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Duration
                            </th>
                        @endif
                        
                        @if($this->isColumnVisible('type'))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Type
                            </th>
                        @endif
                        
                        @if($this->isColumnVisible('status'))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                        @endif
                        
                        @if($this->isColumnVisible('notes'))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Notes
                            </th>
                        @endif
                        
                        @if($this->isColumnVisible('cues_count'))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Cues
                            </th>
                        @endif
                        
                        @if($this->isColumnVisible('created_by'))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Created By
                            </th>
                        @endif
                        
                        @if($this->isColumnVisible('updated_at'))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Last Updated
                            </th>
                        @endif
                        
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($segments as $segment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $segment->id == $activeSegmentId ? 'bg-green-100 dark:bg-green-900/30' : '' }}"
                            wire:key="segment-{{ $segment->id }}">
                            
                            @if($this->isColumnVisible('order'))
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $segment->order }}
                                </td>
                            @endif
                            
                            @if($this->isColumnVisible('name'))
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $segment->name }}
                                    </div>
                                </td>
                            @endif
                            
                            @if($this->isColumnVisible('start_time'))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $segment->start_time }}
                                </td>
                            @endif
                            
                            @if($this->isColumnVisible('end_time'))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $segment->end_time }}
                                </td>
                            @endif
                            
                            @if($this->isColumnVisible('duration'))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $segment->duration_minutes }} min
                                </td>
                            @endif
                            
                            @if($this->isColumnVisible('type'))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ ucfirst($segment->type ?? 'N/A') }}
                                </td>
                            @endif
                            
                            @if($this->isColumnVisible('status'))
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <flux:badge :color="$segment->status === 'completed' ? 'green' : ($segment->status === 'in_progress' ? 'blue' : 'gray')">
                                        {{ ucfirst(str_replace('_', ' ', $segment->status ?? 'pending')) }}
                                    </flux:badge>
                                </td>
                            @endif
                            
                            @if($this->isColumnVisible('notes'))
                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="max-w-xs truncate">
                                        {{ $segment->notes }}
                                    </div>
                                </td>
                            @endif
                            
                            @if($this->isColumnVisible('cues_count'))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $segment->cues->count() }}
                                </td>
                            @endif
                            
                            @if($this->isColumnVisible('created_by'))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $segment->creator->name ?? 'N/A' }}
                                </td>
                            @endif
                            
                            @if($this->isColumnVisible('updated_at'))
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $segment->updated_at->diffForHumans() }}
                                </td>
                            @endif
                            
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($segment->id == $activeSegmentId)
                                    <flux:badge color="green">ACTIVE</flux:badge>
                                @else
                                    <button wire:click="setActiveSegment({{ $segment->id }})" type="button" class="inline-flex items-center px-3 py-1.5 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-md transition">
                                        Set Active
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="20" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <p class="text-lg mb-2">No segments found for this session.</p>
                                <flux:button href="{{ route('sessions.segments.create', $sessionId) }}" variant="primary">
                                    Create First Segment
                                </flux:button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Column Selection Modal -->
    @if($showColumnModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Customize Columns
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Select which columns to display in the Run of Show table
                    </p>
                </div>
                
                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($availableColumns as $columnKey => $columnLabel)
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:click="toggleColumn('{{ $columnKey }}')"
                                    @if($this->isColumnVisible($columnKey)) checked @endif
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                >
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    {{ $columnLabel }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end gap-2">
                    <flux:button wire:click="closeColumnModal" variant="ghost">
                        Cancel
                    </flux:button>
                    <button wire:click="saveColumnPreferences" type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium rounded-md transition">
                        Save Preferences
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
