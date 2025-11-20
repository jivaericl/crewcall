<div class="py-12">
    <div class="mx-auto sm:px-6 lg:px-8">
        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 rounded-md">
                {{ session('message') }}
            </div>
        @endif

        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        <span class="text-2xl font-medium text-gray-600 dark:text-gray-300">
                            {{ substr($user->name, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button 
                        type="button"
                        wire:click.prevent="openEditModal" 
                        class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-600 dark:text-white dark:hover:bg-blue-500">
                        Edit Health & Safety
                    </button>
                    <a href="{{ route('events.travel.index', $eventId) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                        ← Back to Travel
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Assignments -->
            <flux:card class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Assignments</h3>
                
                @if(count($assignments) > 0)
                    <div class="space-y-4">
                        @foreach($assignments as $assignment)
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase">{{ $assignment['type'] }}</span>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $assignment['name'] }}</p>
                                        @if($assignment['details'])
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $assignment['details'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No assignments found for this event.</p>
                @endif
            </flux:card>

            <!-- Health & Safety -->
            <flux:card class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Health & Safety</h3>
                
                <div class="space-y-4">
                    <!-- Dietary Restrictions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Dietary Restrictions
                        </label>
                        @if($user->dietary_restrictions)
                            <p class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-md">
                                {{ $user->dietary_restrictions }}
                            </p>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">None specified</p>
                        @endif
                    </div>

                    <!-- Allergies -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Allergies
                        </label>
                        @if($user->allergies)
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <p class="text-sm text-gray-900 dark:text-white bg-red-50 dark:bg-red-900/20 p-3 rounded-md flex-1">
                                    {{ $user->allergies }}
                                </p>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">None specified</p>
                        @endif
                    </div>

                    <!-- Health Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Additional Health Notes
                        </label>
                        @if($user->health_notes)
                            <p class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-md">
                                {{ $user->health_notes }}
                            </p>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic">None specified</p>
                        @endif
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Travel Information -->
        @if($travel)
            <div class="mt-6">
                <flux:card class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Travel Information</h3>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Flights -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                </svg>
                                Flights ({{ $travel->flights->count() }})
                            </h4>
                            
                            @if($travel->flights->count() > 0)
                                <div class="space-y-3">
                                    @foreach($travel->flights as $flight)
                                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                            <div class="flex justify-between items-start mb-2">
                                                <span class="text-xs font-medium text-blue-600 dark:text-blue-400">{{ $flight->flight_number }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $flight->airline }}</span>
                                            </div>
                                            <div class="space-y-1 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">From:</span>
                                                    <span class="text-gray-900 dark:text-white font-medium">{{ $flight->departure_airport }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">To:</span>
                                                    <span class="text-gray-900 dark:text-white font-medium">{{ $flight->arrival_airport }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Departs:</span>
                                                    <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($flight->departure_time)->format('M d, Y g:i A') }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Arrives:</span>
                                                    <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($flight->arrival_time)->format('M d, Y g:i A') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">No flights booked</p>
                            @endif
                        </div>

                        <!-- Hotel Reservations -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Hotel Reservations ({{ $travel->hotelReservations->count() }})
                            </h4>
                            
                            @if($travel->hotelReservations->count() > 0)
                                <div class="space-y-3">
                                    @foreach($travel->hotelReservations as $reservation)
                                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                            <div class="mb-2">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $reservation->hotel->name }}</span>
                                            </div>
                                            <div class="space-y-1 text-sm">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Check-in:</span>
                                                    <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('M d, Y') }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Check-out:</span>
                                                    <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($reservation->check_out_date)->format('M d, Y') }}</span>
                                                </div>
                                                @if($reservation->room_number)
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600 dark:text-gray-400">Room:</span>
                                                        <span class="text-gray-900 dark:text-white">{{ $reservation->room_number }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">No hotel reservations</p>
                            @endif
                        </div>
                    </div>

                    <!-- Travel Notes -->
                    @if($travel->notes)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Travel Notes</h4>
                            <p class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-md">
                                {{ $travel->notes }}
                            </p>
                        </div>
                    @endif
                </flux:card>
            </div>
        @else
            <div class="mt-6">
                <flux:card class="p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No travel information available for this team member.</p>
                </flux:card>
            </div>
        @endif

        <!-- Edit Health & Safety Modal -->
        @if($showEditModal)
            <flux:modal wire:model.live="showEditModal">
        <flux:modal.content>
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Edit Health & Safety Information</h3>
                
                <div class="space-y-4">
                    <!-- Dietary Restrictions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dietary Restrictions</label>
                        <textarea 
                            wire:model="editDietaryRestrictions" 
                            placeholder="e.g., Gluten-free, Vegan, Vegetarian, Lactose intolerant"
                            rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">List any dietary restrictions or preferences</p>
                    </div>

                    <!-- Allergies -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Allergies</label>
                        <textarea 
                            wire:model="editAllergies" 
                            placeholder="e.g., Nuts, Shellfish, Peanuts, Dairy"
                            rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">List any food or environmental allergies</p>
                    </div>

                    <!-- Health Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Additional Health Notes</label>
                        <textarea 
                            wire:model="editHealthNotes" 
                            placeholder="Any other health information that should be noted"
                            rows="4"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Any additional health or medical information</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <flux:button wire:click="closeEditModal" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button wire:click="saveHealthSafety" variant="primary">
                        Save Changes
                    </flux:button>
                </div>
            </div>
            </flux:modal.content>
        </flux:modal>
        @endif
    </div>
</div>
