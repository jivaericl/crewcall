<div>
    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Calendar</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                View and manage your production schedule
            </p>
        </div>
        <div>
            <a href="{{ route('events.calendar.index', $eventId) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                List View
            </a>
        </div>
    </div>

    {{-- Filter Toggles --}}
    <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex flex-wrap gap-3">
            <button wire:click="toggleMilestones" 
                    class="inline-flex items-center px-4 py-2 rounded-md transition-colors {{ $showMilestones ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                Production Milestones
            </button>
            
            <button wire:click="toggleOutOfOffice" 
                    class="inline-flex items-center px-4 py-2 rounded-md transition-colors {{ $showOutOfOffice ? 'bg-amber-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                <span class="w-3 h-3 rounded-full bg-amber-500 mr-2"></span>
                Out of Office
            </button>
            
            <button wire:click="toggleCalls" 
                    class="inline-flex items-center px-4 py-2 rounded-md transition-colors {{ $showCalls ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                Calls
            </button>
            
            <button wire:click="toggleSessions" 
                    class="inline-flex items-center px-4 py-2 rounded-md transition-colors {{ $showSessions ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                <span class="w-3 h-3 rounded-full bg-purple-500 mr-2"></span>
                Sessions
            </button>
        </div>
    </div>

    {{-- Calendar Container --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div id="calendar"></div>
    </div>

    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let calendar;
            const calendarEl = document.getElementById('calendar');
            const eventId = {{ $eventId }};
            
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Filter states
            let filterStates = {
                showMilestones: @json($showMilestones),
                showOutOfOffice: @json($showOutOfOffice),
                showCalls: @json($showCalls),
                showSessions: @json($showSessions)
            };
            
            // Initialize calendar
            function initCalendar() {
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    height: 'auto',
                    editable: true,
                    droppable: true,
                    eventDurationEditable: true,
                    eventStartEditable: true,
                    
                    // Fetch events from API
                    events: function(info, successCallback, failureCallback) {
                        fetch(`/api/events/${eventId}/calendar/events?start=${info.startStr}&end=${info.endStr}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            credentials: 'same-origin'
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Filter events based on toggle states
                            const filteredEvents = data.filter(event => {
                                if (event.extendedProps.type === 'calendar_item') {
                                    if (event.extendedProps.calendar_type === 'milestone') return filterStates.showMilestones;
                                    if (event.extendedProps.calendar_type === 'out_of_office') return filterStates.showOutOfOffice;
                                    if (event.extendedProps.calendar_type === 'call') return filterStates.showCalls;
                                }
                                if (event.extendedProps.type === 'session') return filterStates.showSessions;
                                return true;
                            });
                            successCallback(filteredEvents);
                        })
                        .catch(error => {
                            console.error('Error fetching calendar events:', error);
                            failureCallback(error);
                        });
                    },
                    
                    // Handle event drop (drag and drop)
                    eventDrop: function(info) {
                        const eventId = info.event.id.replace('calendar-', '');
                        
                        // Only update calendar items, not sessions
                        if (!info.event.id.startsWith('calendar-')) {
                            info.revert();
                            alert('Sessions cannot be rescheduled from the calendar view.');
                            return;
                        }
                        
                        fetch(`/api/events/{{ $eventId }}/calendar/${eventId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({
                                start: info.event.start.toISOString(),
                                end: info.event.end ? info.event.end.toISOString() : info.event.start.toISOString(),
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Success - event already moved
                            } else {
                                info.revert();
                                alert('Failed to update event');
                            }
                        })
                        .catch(error => {
                            console.error('Error updating event:', error);
                            info.revert();
                            alert('Error updating event');
                        });
                    },
                    
                    // Handle event click
                    eventClick: function(info) {
                        if (info.event.extendedProps.url) {
                            window.location.href = info.event.extendedProps.url;
                        }
                    },
                    
                    // Handle date click (create new event)
                    dateClick: function(info) {
                        window.location.href = `/events/{{ $eventId }}/calendar/create?date=${info.dateStr}`;
                    }
                });
                
                calendar.render();
            }
            
            initCalendar();
            
            // Listen for filter changes from Livewire
            window.addEventListener('filterChanged', (event) => {
                // Update filter states from Livewire component
                filterStates = event.detail;
                calendar.refetchEvents();
            });
        });
    </script>
    @endpush
</div>
