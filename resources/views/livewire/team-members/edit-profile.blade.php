<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit User Profile</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $user->name }}</p>
            </div>
            <flux:button href="{{ route('events.team-members.show', [$eventId, $userId]) }}" variant="ghost">
                Back to Profile
            </flux:button>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded">
                {{ session('message') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <form wire:submit.prevent="save" class="p-6 space-y-8">
                
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="md:col-span-2">
                            <flux:input 
                                wire:model="name" 
                                label="Full Name"
                                placeholder="John Doe"
                                required
                            />
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- First Name -->
                        <div>
                            <flux:input 
                                wire:model="first_name" 
                                label="First Name"
                                placeholder="John"
                            />
                            @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <flux:input 
                                wire:model="last_name" 
                                label="Last Name"
                                placeholder="Doe"
                            />
                            @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2">
                            <flux:input 
                                wire:model="email" 
                                type="email"
                                label="Email Address"
                                placeholder="john@example.com"
                                required
                            />
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Timezone -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Timezone
                            </label>
                            <select 
                                wire:model="timezone" 
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach(timezone_identifiers_list() as $tz)
                                    <option value="{{ $tz }}">{{ $tz }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                User's personal timezone for viewing event times
                            </p>
                            @error('timezone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Health & Safety Information -->
                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Health & Safety Information</h3>
                    <div class="space-y-6">
                        <!-- Dietary Restrictions -->
                        <div>
                            <flux:textarea 
                                wire:model="dietary_restrictions" 
                                label="Dietary Restrictions"
                                placeholder="e.g., Vegetarian, Gluten-free, Nut allergy..."
                                rows="3"
                            />
                            @error('dietary_restrictions') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Allergies -->
                        <div>
                            <flux:textarea 
                                wire:model="allergies" 
                                label="Allergies"
                                placeholder="List any known allergies..."
                                rows="3"
                            />
                            @error('allergies') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Health Notes -->
                        <div>
                            <flux:textarea 
                                wire:model="health_notes" 
                                label="Health Notes"
                                placeholder="Any other health information that should be noted..."
                                rows="3"
                            />
                            @error('health_notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact Information -->
                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Emergency Contact</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <flux:input 
                                wire:model="emergency_contact_first_name" 
                                label="First Name"
                                placeholder="John"
                            />
                            @error('emergency_contact_first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <flux:input 
                                wire:model="emergency_contact_last_name" 
                                label="Last Name"
                                placeholder="Doe"
                            />
                            @error('emergency_contact_last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Relationship -->
                        <div>
                            <flux:input 
                                wire:model="emergency_contact_relationship" 
                                label="Relationship"
                                placeholder="e.g., Spouse, Parent, Sibling"
                            />
                            @error('emergency_contact_relationship') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <flux:input 
                                wire:model="emergency_contact_phone" 
                                type="tel"
                                label="Phone Number"
                                placeholder="(555) 123-4567"
                            />
                            @error('emergency_contact_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2">
                            <flux:input 
                                wire:model="emergency_contact_email" 
                                type="email"
                                label="Email Address"
                                placeholder="emergency@example.com"
                            />
                            @error('emergency_contact_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('events.team-members.show', [$eventId, $userId]) }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium rounded-md bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-600 dark:text-white dark:hover:bg-blue-500">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
