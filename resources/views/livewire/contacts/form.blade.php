<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    {{ $contactId ? 'Edit Contact' : 'Create Contact' }}
                </h2>

                <form wire:submit="save">
                    <!-- Name Fields -->
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
                        <flux:select wire:model="type" required>
                            <option value="client">Client</option>
                            <option value="producer">Producer</option>
                            <option value="vendor">Vendor</option>
                            <option value="staff">Staff</option>
                            <option value="other">Other</option>
                        </flux:select>
                        @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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

                    <!-- Active Status -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3">
                        <flux:button type="button" variant="ghost" wire:click="$redirect('{{ route('events.contacts.index', $eventId) }}')">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ $contactId ? 'Update Contact' : 'Create Contact' }}
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tag Creation Modal -->
    @if($showTagModal)
        <flux:modal wire:model.live="showTagModal">
            <flux:modal.content>
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
                    <flux:button type="button" variant="ghost" wire:click="closeTagModal">
                        Cancel
                    </flux:button>
                    <flux:button type="button" variant="primary" wire:click="createTag">
                        Create Tag
                    </flux:button>
                </div>
            </flux:modal.content>
        </flux:modal>
    @endif
</div>
