<div x-data="{ clockInterval: null }" x-init="clockInterval = setInterval(() => $wire.updateClock(), 1000)" x-on:destroy="clearInterval(clockInterval)" class="py-6">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
                    Show Calling - {{ $event->name }}
                </h2>
                @if($selectedSession)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $selectedSession->name }} • {{ \Carbon\Carbon::parse($selectedSession->start_date)->format('M d, Y') }}
                    </p>
                @endif
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100 tabular-nums">
                    {{ $clockTime }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Current Time
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('message') }}
            </div>
        @endif

        <!-- Session Selector & Controls -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-4">
            <div class="flex flex-wrap gap-4 items-center justify-between">
                <div class="flex gap-2 items-center flex-1">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Session:</label>
                    <select wire:model.live="selectedSessionId" wire:change="selectSession($event.target.value)" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}">{{ $session->name }} - {{ \Carbon\Carbon::parse($session->start_date)->format('M d') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <flux:button wire:click="setViewMode('table')" variant="{{ $viewMode === 'table' ? 'primary' : 'ghost' }}" size="sm">
                        Table View
                    </flux:button>
                    <flux:button wire:click="setViewMode('timeline')" variant="{{ $viewMode === 'timeline' ? 'primary' : 'ghost' }}" size="sm">
                        Timeline View
                    </flux:button>
                </div>
            </div>
        </div>

        @if($viewMode === 'table')
            @include('livewire.show-call.partials.table-view')
        @else
            @include('livewire.show-call.partials.timeline-view')
        @endif
    </div>
</div>
