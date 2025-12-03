<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $hotel->name }} - Reservations</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Manage all reservations for this hotel
                </p>
                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <strong>Address:</strong> {{ $hotel->address }}, {{ $hotel->city }}, {{ $hotel->state }} {{ $hotel->zip }}
                </div>
            </div>
            <div class="flex space-x-2">
                <button wire:click="openAddModal" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium rounded-md transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Reservation
                </button>
                <a href="{{ route('events.travel.hotels', $eventId) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 hover:bg-gray-700 dark:hover:bg-gray-600 text-white font-medium rounded-md transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Hotels
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded relative">
                {{ session('message') }}
            </div>
        @endif

        <!-- Search -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Search by person name, email, or reservation number..." />
                </div>
            </div>
        </div>

        <!-- Reservations Table -->
        <flux:card>
            @if($reservations->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Person</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reservation Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Check-in</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Check-out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nights</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Notes</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($reservations as $reservation)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <!-- Person -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $reservation->travel->user->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $reservation->travel->user->email }}
                                        </div>
                                    </td>

                                    <!-- Reservation Number -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $reservation->reservation_number ?: '—' }}
                                        </div>
                                    </td>

                                    <!-- Check-in -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $reservation->check_in_date->format('M j, Y') }}
                                        </div>
                                    </td>

                                    <!-- Check-out -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $reservation->check_out_date->format('M j, Y') }}
                                        </div>
                                    </td>

                                    <!-- Nights -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $reservation->check_in_date->diffInDays($reservation->check_out_date) }}
                                        </div>
                                    </td>

                                    <!-- Notes -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $reservation->notes ? \Str::limit($reservation->notes, 50) : '—' }}
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <flux:button size="sm" variant="ghost" wire:click="openEditModal({{ $reservation->id }})" title="Edit">
                                                <x-action-icon action="edit" />
                                            </flux:button>
                                            <flux:button size="sm" variant="ghost" wire:click="delete({{ $reservation->id }})" wire:confirm="Are you sure you want to delete this reservation?" title="Delete">
                                                <x-action-icon action="delete" />
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $reservations->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No reservations</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Get started by adding a reservation for this hotel.
                    </p>
                    <div class="mt-6">
                        <button wire:click="openAddModal" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium rounded-md transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Reservation
                        </button>
                    </div>
                </div>
            @endif
        </flux:card>
    </div>

    <!-- Add Modal -->
    @if($showAddModal)
        <flux:modal wire:model.live="showAddModal" class="space-y-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add Hotel Reservation</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Add a new reservation for {{ $hotel->name }}</p>
            </div>

            <div class="space-y-4">
                <div>
                    <flux:field>
                        <flux:label>Person / Travel</flux:label>
                        <flux:select wire:model="travelId">
                            <option value="">Select a person...</option>
                            @foreach($travels as $travel)
                                <option value="{{ $travel->id }}">{{ $travel->user->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('travelId') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Reservation Number (Optional)</flux:label>
                        <flux:input wire:model="reservationNumber" placeholder="e.g., CONF123456" />
                        @error('reservationNumber') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>Check-in Date</flux:label>
                            <flux:input type="date" wire:model="checkInDate" />
                            @error('checkInDate') <flux:error>{{ $message }}</flux:error> @enderror
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Check-out Date</flux:label>
                            <flux:input type="date" wire:model="checkOutDate" />
                            @error('checkOutDate') <flux:error>{{ $message }}</flux:error> @enderror
                        </flux:field>
                    </div>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Notes (Optional)</flux:label>
                        <flux:textarea wire:model="notes" rows="3" placeholder="Any special requests or notes..." />
                        @error('notes') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="closeAddModal">Cancel</flux:button>
                <flux:button wire:click="save">Add Reservation</flux:button>
            </div>
        </flux:modal>
    @endif

    <!-- Edit Modal -->
    @if($showEditModal)
        <flux:modal wire:model.live="showEditModal" class="space-y-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Hotel Reservation</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update reservation details for {{ $hotel->name }}</p>
            </div>

            <div class="space-y-4">
                <div>
                    <flux:field>
                        <flux:label>Person / Travel</flux:label>
                        <flux:select wire:model="travelId">
                            <option value="">Select a person...</option>
                            @foreach($travels as $travel)
                                <option value="{{ $travel->id }}">{{ $travel->user->name }}</option>
                            @endforeach
                        </flux:select>
                        @error('travelId') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Reservation Number (Optional)</flux:label>
                        <flux:input wire:model="reservationNumber" placeholder="e.g., CONF123456" />
                        @error('reservationNumber') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <flux:field>
                            <flux:label>Check-in Date</flux:label>
                            <flux:input type="date" wire:model="checkInDate" />
                            @error('checkInDate') <flux:error>{{ $message }}</flux:error> @enderror
                        </flux:field>
                    </div>
                    <div>
                        <flux:field>
                            <flux:label>Check-out Date</flux:label>
                            <flux:input type="date" wire:model="checkOutDate" />
                            @error('checkOutDate') <flux:error>{{ $message }}</flux:error> @enderror
                        </flux:field>
                    </div>
                </div>

                <div>
                    <flux:field>
                        <flux:label>Notes (Optional)</flux:label>
                        <flux:textarea wire:model="notes" rows="3" placeholder="Any special requests or notes..." />
                        @error('notes') <flux:error>{{ $message }}</flux:error> @enderror
                    </flux:field>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="closeEditModal">Cancel</flux:button>
                <flux:button wire:click="update">Update Reservation</flux:button>
            </div>
        </flux:modal>
    @endif
</div>
