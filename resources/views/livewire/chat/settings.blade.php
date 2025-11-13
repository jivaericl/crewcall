<div class="flex flex-col bg-gray-50 dark:bg-gray-900" style="height: calc(100vh - 4rem);">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat Settings</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Customize your chat experience
                </p>
            </div>
            <a href="{{ route('chat.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="flex-1 overflow-y-auto p-6">
        <div class="max-w-2xl mx-auto">
            @if (session()->has('message'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit="save" class="space-y-6">
                <!-- Sound Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sound & Notifications</h2>
                    
                    <div class="space-y-4">
                        <!-- Sound Enabled -->
                        <div class="flex items-start gap-3">
                            <input 
                                type="checkbox" 
                                wire:model="chat_sound_enabled" 
                                id="sound_enabled"
                                class="mt-1 w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            >
                            <label for="sound_enabled" class="cursor-pointer">
                                <span class="text-sm font-medium text-gray-900 dark:text-white block">Play sound on new messages</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Hear a notification sound when you receive a message</p>
                            </label>
                        </div>

                        <!-- Notifications Enabled -->
                        <div class="flex items-start gap-3">
                            <input 
                                type="checkbox" 
                                wire:model="chat_notifications_enabled" 
                                id="notifications_enabled"
                                class="mt-1 w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            >
                            <label for="notifications_enabled" class="cursor-pointer">
                                <span class="text-sm font-medium text-gray-900 dark:text-white block">Enable chat notifications</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Show notifications for new messages</p>
                            </label>
                        </div>

                        <!-- Desktop Notifications -->
                        <div class="flex items-start gap-3">
                            <input 
                                type="checkbox" 
                                wire:model="chat_desktop_notifications" 
                                id="desktop_notifications"
                                class="mt-1 w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            >
                            <label for="desktop_notifications" class="cursor-pointer">
                                <span class="text-sm font-medium text-gray-900 dark:text-white block">Desktop notifications</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Show browser notifications even when tab is not active</p>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Widget Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Chat Widget</h2>
                    
                    <div class="space-y-4">
                        <!-- Widget Enabled -->
                        <div class="flex items-start gap-3">
                            <input 
                                type="checkbox" 
                                wire:model="chat_widget_enabled" 
                                id="widget_enabled"
                                class="mt-1 w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            >
                            <label for="widget_enabled" class="cursor-pointer">
                                <span class="text-sm font-medium text-gray-900 dark:text-white block">Show floating chat button</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Display the chat bubble in the bottom right corner</p>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium px-6 py-2 rounded-lg transition-colors">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
