<div>
@if($isOpen && $user)
<div class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="close"></div>
    
    <!-- Modal -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
            <!-- Close button -->
            <button wire:click="close" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <!-- Profile Content -->
            <div class="text-center">
                <!-- Avatar -->
                <div class="w-24 h-24 rounded-full bg-blue-500 text-white text-3xl flex items-center justify-center mx-auto mb-4 font-semibold">
                    {{ substr($user->name, 0, 1) }}
                </div>
                
                <!-- Name -->
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $user->name }}
                </h2>
                
                <!-- Professional Info -->
                @if(($user->title ?? false) || ($user->company ?? false) || ($eventRoleName ?? false))
                    <div class="space-y-3 text-left mb-6 mt-6">
                        @if($user->title ?? false)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 9.75C21 16.958 16.075 23 12 23S3 16.958 3 9.75c0-.957.164-1.887.468-2.766L9 11"></path>
                                </svg>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Title</p>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $user->title }}</p>
                                </div>
                            </div>
                        @endif

                        @if($user->company ?? false)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4a2 2 0 012-2h2V5a2 2 0 012-2h4a2 2 0 012 2v10h2a2 2 0 012 2v4"></path>
                                </svg>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Company</p>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $user->company }}</p>
                                </div>
                            </div>
                        @endif

                        @if($eventRoleName ?? false)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Show Role</p>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ $eventRoleName }}</p>
                                    @if($eventName)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $eventName }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Contact Info -->
                <div class="space-y-3 text-left mb-6">
                    <!-- Email -->
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Email</p>
                            <a href="mailto:{{ $user->email }}" class="text-sm text-gray-700 dark:text-gray-300 truncate hover:text-blue-600 dark:hover:text-blue-400">
                                {{ $user->email }}
                            </a>
                        </div>
                    </div>
                    
                    <!-- Phone (if exists) -->
                    @if($user->phone ?? false)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Phone</p>
                                <a href="tel:{{ preg_replace('/[^\d\+]/', '', $user->phone) }}" class="text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">
                                    {{ $user->phone }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <a href="mailto:{{ $user->email }}" class="flex-1 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email
                    </a>
                    <button wire:click="startDirectMessage" class="flex-1 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium px-4 py-2 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Message
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
</div>
