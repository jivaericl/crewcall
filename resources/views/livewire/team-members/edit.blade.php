<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 rounded-md">
                {{ session('message') }}
            </div>
        @endif

        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Health & Safety Information</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $user->name }} ({{ $user->email }})</p>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-6">
            <form wire:submit.prevent="save">
                <div class="space-y-6">
                    <!-- Dietary Restrictions -->
                    <div>
                        <label for="dietary_restrictions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Dietary Restrictions
                        </label>
                        <textarea 
                            wire:model="dietary_restrictions" 
                            id="dietary_restrictions"
                            placeholder="e.g., Gluten-free, Vegan, Vegetarian, Lactose intolerant"
                            rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">List any dietary restrictions or preferences</p>
                    </div>

                    <!-- Allergies -->
                    <div>
                        <label for="allergies" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Allergies
                        </label>
                        <textarea 
                            wire:model="allergies" 
                            id="allergies"
                            placeholder="e.g., Nuts, Shellfish, Peanuts, Dairy"
                            rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">List any food or environmental allergies</p>
                    </div>

                    <!-- Health Notes -->
                    <div>
                        <label for="health_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Additional Health Notes
                        </label>
                        <textarea 
                            wire:model="health_notes" 
                            id="health_notes"
                            placeholder="Any other health information that should be noted"
                            rows="4"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Any additional health or medical information</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('events.team-members.show', [$eventId, $userId]) }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        Cancel
                    </a>
                    <button 
                        type="submit"
                        class="px-4 py-2 text-sm font-medium rounded-md bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-500 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
