<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('Photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remove Photo') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 dark:text-white">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>

        <!-- Timezone -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="timezone" value="{{ __('Timezone') }}" />
            <select id="timezone" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" wire:model="state.timezone">
                @foreach(timezone_identifiers_list() as $tz)
                    <option value="{{ $tz }}">{{ $tz }}</option>
                @endforeach
            </select>
            <x-input-error for="timezone" class="mt-2" />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Your personal timezone for viewing event times</p>
        </div>

        <!-- Emergency Contact Section Header -->
        <div class="col-span-6 sm:col-span-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Emergency Contact</h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Provide emergency contact information for event organizers.</p>
        </div>

        <!-- Emergency Contact First Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="emergency_contact_first_name" value="{{ __('Emergency Contact First Name') }}" />
            <x-input id="emergency_contact_first_name" type="text" class="mt-1 block w-full" wire:model="state.emergency_contact_first_name" autocomplete="off" />
            <x-input-error for="emergency_contact_first_name" class="mt-2" />
        </div>

        <!-- Emergency Contact Last Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="emergency_contact_last_name" value="{{ __('Emergency Contact Last Name') }}" />
            <x-input id="emergency_contact_last_name" type="text" class="mt-1 block w-full" wire:model="state.emergency_contact_last_name" autocomplete="off" />
            <x-input-error for="emergency_contact_last_name" class="mt-2" />
        </div>

        <!-- Emergency Contact Relationship -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="emergency_contact_relationship" value="{{ __('Relationship') }}" />
            <x-input id="emergency_contact_relationship" type="text" class="mt-1 block w-full" wire:model="state.emergency_contact_relationship" placeholder="e.g., Spouse, Parent, Sibling" autocomplete="off" />
            <x-input-error for="emergency_contact_relationship" class="mt-2" />
        </div>

        <!-- Emergency Contact Phone -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="emergency_contact_phone" value="{{ __('Emergency Contact Phone') }}" />
            <x-input id="emergency_contact_phone" type="tel" class="mt-1 block w-full" wire:model="state.emergency_contact_phone" placeholder="(555) 123-4567" autocomplete="off" />
            <x-input-error for="emergency_contact_phone" class="mt-2" />
        </div>

        <!-- Emergency Contact Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="emergency_contact_email" value="{{ __('Emergency Contact Email') }}" />
            <x-input id="emergency_contact_email" type="email" class="mt-1 block w-full" wire:model="state.emergency_contact_email" autocomplete="off" />
            <x-input-error for="emergency_contact_email" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
