<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $contactId ? 'Edit Contact' : 'Create Contact' }}
            </h2>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="p-6">
                <form wire:submit="save">                   <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <flux:label>First Name *</flux:label>
                            <flux:input wire:model="first_name" required />
                            @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <flux:label>Last Name *</flux:label>
                            <flux:input wire:model="last_name" required />
                            @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Contact Type -->
                    <div class="mb-6">
                        <flux:label>Contact Type *</flux:label>
                        <flux:select wire:model="contact_type" required>
                            <option value="client">Client</option>
                            <option value="producer">Producer</option>
                            <option value="vendor">Vendor</option>
                            <option value="staff">Staff</option>
                            <option value="other">Other</option>
                        </flux:select>
                        @error('contact_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Company & Title -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <flux:label>Company</flux:label>
                            <flux:input wire:model="company" />
                            @error('company') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <flux:label>Title</flux:label>
                            <flux:input wire:model="title" />
                            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <flux:label>Email</flux:label>
                            <flux:input type="email" wire:model="email" />
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <flux:label>Phone</flux:label>
                            <flux:input wire:model="phone" />
                            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-6">
                        <flux:label>Address</flux:label>
                        <flux:input wire:model="address" />
                        @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- City, State, Zip -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <flux:label>City</flux:label>
                            <flux:input wire:model="city" />
                            @error('city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <flux:label>State</flux:label>
                            <flux:input wire:model="state" />
                            @error('state') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <flux:label>Zip</flux:label>
                            <flux:input wire:model="zip" />
                            @error('zip') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Country -->
                    <div class="mb-6">
                        <flux:label>Country</flux:label>
                        <flux:input wire:model="country" />
                        @error('country') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <flux:label>Notes</flux:label>
                        <flux:textarea wire:model="notes" rows="4" />
                        @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tags -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <flux:label>Tags</flux:label>
                            <flux:button type="button" size="sm" variant="ghost" wire:click="openTagModal">
                                + Create Tag
                            </flux:button>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <label class="inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        wire:model="selectedTags"
                                        value="{{ $tag->id }}"
                                        class="sr-only peer"
                                    />
                                    <span class="px-3 py-1 rounded-full text-sm font-medium transition-all
                                        peer-checked:ring-2 peer-checked:ring-offset-2"
                                        style="background-color: {{ $tag->color }}20; color: {{ $tag->color }}; ring-color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Sessions Assignment -->
                    @if($sessions->count() > 0)
                    <div class="mb-6">
                        <flux:label>Assign to Sessions</flux:label>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($sessions as $session)
                                <label class="inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        wire:model="selectedSessions"
                                        value="{{ $session->id }}"
                                        class="sr-only peer"
                                    />
                                    <span class="px-3 py-1 rounded-full text-sm font-medium transition-all bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                        peer-checked:bg-blue-100 dark:peer-checked:bg-blue-900 peer-checked:text-blue-700 dark:peer-checked:text-blue-300
                                        peer-checked:ring-2 peer-checked:ring-blue-500 peer-checked:ring-offset-2">
                                        {{ $session->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Content Assignment -->
                    @if($contentFiles->count() > 0)
                    <div class="mb-6">
                        <flux:label>Assign Content Files</flux:label>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($contentFiles as $contentFile)
                                <label class="inline-flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        wire:model="selectedContentFiles"
                                        value="{{ $contentFile->id }}"
                                        class="sr-only peer"
                                    />
                                    <span class="px-3 py-1 rounded-full text-sm font-medium transition-all bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                        peer-checked:bg-green-100 dark:peer-checked:bg-green-900 peer-checked:text-green-700 dark:peer-checked:text-green-300
                                        peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:ring-offset-2">
                                        {{ $contentFile->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                    </div>

                    <!-- Custom Fields -->
                    @if($customFieldsList->count() > 0)
                    <div class="col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Custom Fields</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($customFieldsList as $field)
                                <div class="{{ $field->field_type === 'textarea' ? 'md:col-span-2' : '' }}">
                                    <flux:field>
                                        <flux:label for="custom_{{ $field->id }}">
                                            {{ $field->name }}
                                            @if($field->is_required) <span class="text-red-500">*</span> @endif
                                        </flux:label>

                                        @if($field->field_type === 'text')
                                            <flux:input
                                                wire:model="customFields.{{ $field->id }}"
                                                id="custom_{{ $field->id }}"
                                                type="text"
                                            />
                                        @elseif($field->field_type === 'number')
                                            <flux:input
                                                wire:model="customFields.{{ $field->id }}"
                                                id="custom_{{ $field->id }}"
                                                type="number"
                                                step="any"
                                            />
                                        @elseif($field->field_type === 'date')
                                            <flux:input
                                                wire:model="customFields.{{ $field->id }}"
                                                id="custom_{{ $field->id }}"
                                                type="date"
                                            />
                                        @elseif($field->field_type === 'textarea')
                                            <flux:textarea
                                                wire:model="customFields.{{ $field->id }}"
                                                id="custom_{{ $field->id }}"
                                                rows="3"
                                            />
                                        @elseif($field->field_type === 'select')
                                            <flux:select wire:model="customFields.{{ $field->id }}" id="custom_{{ $field->id }}">
                                                <option value="">-- Select --</option>
                                                @if($field->options)
                                                    @foreach($field->options as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                @endif
                                            </flux:select>
                                        @elseif($field->field_type === 'checkbox')
                                            <div class="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    wire:model="customFields.{{ $field->id }}"
                                                    id="custom_{{ $field->id }}"
                                                    value="1"
                                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                >
                                            </div>
                                        @endif

                                        @error('customFields.' . $field->id)
                                            <flux:error>{{ $message }}</flux:error>
                                        @enderror
                                    </flux:field>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('events.contacts.index', $eventId) }}">
                            <flux:button type="button" variant="ghost">
                                Cancel
                            </flux:button>
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium rounded-md transition">
                            {{ $contactId ? 'Update Contact' : 'Create Contact' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tag Creation Modal -->
    <div x-data="{ show: @entangle('showTagModal') }"
         x-show="show"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false"></div>

            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Create New Tag</h3>

                <div class="space-y-4">
                    <div>
                        <flux:label>Tag Name</flux:label>
                        <flux:input wire:model="newTagName" />
                    </div>

                    <div>
                        <flux:label>Color</flux:label>
                        <input type="color" wire:model="newTagColor" class="w-full h-10 rounded border-gray-300 dark:border-gray-600" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="closeTagModal" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="button" wire:click="createTag" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Create Tag
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
