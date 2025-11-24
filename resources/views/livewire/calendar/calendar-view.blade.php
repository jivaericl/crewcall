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
    <div class="mb-6">
        <div class="flex flex-wrap gap-3">
        <button wire:click="toggleMilestones" 
                class="inline-flex items-center px-4 py-2 rounded-lg border-2 transition-all {{ $showMilestones ? 'bg-green-50 dark:bg-green-900/20 border-green-500 dark:border-green-600 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400 opacity-50' }}">
            <span class="w-3 h-3 rounded-full mr-2" style="background-color: #10B981;"></span>
            <span class="font-medium">Production Milestones</span>
        </button>
        
        <button wire:click="toggleOutOfOffice" 
                class="inline-flex items-center px-4 py-2 rounded-lg border-2 transition-all {{ $showOutOfOffice ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-500 dark:border-amber-600 text-amber-700 dark:text-amber-300' : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400 opacity-50' }}">
            <span class="w-3 h-3 rounded-full mr-2" style="background-color: #F59E0B;"></span>
            <span class="font-medium">Out of Office</span>
        </button>
        
        <button wire:click="toggleCalls" 
                class="inline-flex items-center px-4 py-2 rounded-lg border-2 transition-all {{ $showCalls ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-500 dark:border-blue-600 text-blue-700 dark:text-blue-300' : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400 opacity-50' }}">
            <span class="w-3 h-3 rounded-full mr-2" style="background-color: #3B82F6;"></span>
            <span class="font-medium">Calls</span>
        </button>
        
        <button wire:click="toggleSessions" 
                class="inline-flex items-center px-4 py-2 rounded-lg border-2 transition-all {{ $showSessions ? 'bg-purple-50 dark:bg-purple-900/20 border-purple-500 dark:border-purple-600 text-purple-700 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400 opacity-50' }}">
            <span class="w-3 h-3 rounded-full mr-2" style="background-color: #8B5CF6;"></span>
            <span class="font-medium">Sessions</span>
        </button>
        </div>
    </div>
    {{-- Calendar Container --}}
    <div wire:ignore>
        <div id="calendar" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4"></div>
    </div>

    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        // Initialize calendar immediately
        let calendar; // Global scope for filter access
        (function() {
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
                    
                    // Timezone handling - use local timezone
                    timeZone: 'local',
                    
                    height: 'auto',
                    editable: true,
                    droppable: true,
                    eventDurationEditable: true,
                    eventStartEditable: true,
                    
                    // Business hours highlighting (8am - 6pm)
                    businessHours: {
                        daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday
                        startTime: '08:00',
                        endTime: '18:00'
                    },
                    
                    // Week/Day view settings
                    slotMinTime: '06:00:00',
                    slotMaxTime: '22:00:00',
                    slotDuration: '00:30:00',
                    slotLabelInterval: '01:00',
                    
                    // Start week on Sunday
                    firstDay: 0,
                    
                    // Show week numbers
                    weekNumbers: false,
                    
                    // Event display settings
                    eventDisplay: 'block',
                    eventTimeFormat: {
                        hour: 'numeric',
                        minute: '2-digit',
                        meridiem: 'short'
                    },
                    
                    // Now indicator for current time
                    nowIndicator: true,
                    
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
            
            // Listen for filter updates from Livewire
            window.addEventListener('filterChanged', function(event) {
                if (!calendar) {
                    console.error('Calendar not initialized');
                    return;
                }
                
                // Livewire dispatch sends data as first element of detail array
                const newFilterStates = event.detail[0] || event.detail;
                
                // Update filter states from Livewire component
                filterStates = {
                    showMilestones: newFilterStates.showMilestones,
                    showOutOfOffice: newFilterStates.showOutOfOffice,
                    showCalls: newFilterStates.showCalls,
                    showSessions: newFilterStates.showSessions
                };
                
                    // Update event visibility client-side without refetching
                    if (calendar) {
                        const allEvents = calendar.getEvents();
                        allEvents.forEach(event => {
                            const eventType = event.extendedProps.type;
                            let shouldShow = true;
                            
                            if (eventType === 'milestone' && !filterStates.showMilestones) shouldShow = false;
                            if (eventType === 'out_of_office' && !filterStates.showOutOfOffice) shouldShow = false;
                            if (eventType === 'call' && !filterStates.showCalls) shouldShow = false;
                            if (eventType === 'session' && !filterStates.showSessions) shouldShow = false;
                            
                            // Use setProp to update display property
                            event.setProp('display', shouldShow ? 'auto' : 'none');
                        });
                        
                        // Re-render calendar to ensure it stays visible
                        calendar.render();
                    }
            });
        })();
    </script>
    @endpush
</div>
