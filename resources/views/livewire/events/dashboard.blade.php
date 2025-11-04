<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $event->name }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $event->start_date->format('M d, Y') }} - {{ $event->end_date->format('M d, Y') }}
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Sessions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Sessions</dt>
                            <dd class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['sessions'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Contacts -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Contacts</dt>
                            <dd class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['contacts'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Team</dt>
                            <dd class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['team_members'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Comments -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Comments</dt>
                            <dd class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['comments'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-8">
                <!-- Upcoming Sessions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Upcoming Sessions</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Next 7 days</p>
                    </div>
                    <div class="p-6">
                        @if($upcomingSessions->count() > 0)
                            <div class="space-y-4">
                                @foreach($upcomingSessions as $session)
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="text-center">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $session->start_date->format('M') }}
                                                </div>
                                                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                                    {{ $session->start_date->format('d') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <a href="{{ route('events.sessions.edit', ['eventId' => $eventId, 'sessionId' => $session->id]) }}" 
                                               class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                                                {{ $session->name }}
                                            </a>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $session->start_date->format('g:i A') }} - {{ $session->end_date->format('g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">No upcoming sessions</p>
                        @endif
                    </div>
                </div>

                <!-- Team Members -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Team Members</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Creator -->
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-medium">
                                        {{ substr($event->creator->name ?? 'U', 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $event->creator->name ?? 'Unknown' }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Event Creator</p>
                                </div>
                            </div>

                            <!-- Team Members -->
                            @foreach($teamMembers as $member)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-500 flex items-center justify-center text-white font-medium">
                                            {{ substr($member->user->name ?? 'U', 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $member->user->name ?? 'Unknown' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $member->role->name ?? 'Team Member' }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Key Contacts -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Key Contacts</h3>
                    </div>
                    <div class="p-6">
                        @if($keyContacts->count() > 0)
                            <div class="space-y-4">
                                @foreach($keyContacts as $contact)
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                {{ $contact->contact_type === 'client' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                                {{ ucfirst($contact->contact_type) }}
                                            </span>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $contact->full_name }}
                                            </p>
                                            @if($contact->company)
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $contact->company }}</p>
                                            @endif
                                            @if($contact->email)
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $contact->email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">No contacts yet</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-8">
                <!-- Recent Comments -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Comments</h3>
                    </div>
                    <div class="p-6">
                        @if($recentComments->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentComments as $comment)
                                    <div class="border-l-2 border-blue-500 pl-4">
                                        <p class="text-sm text-gray-900 dark:text-white">{{ Str::limit($comment->content, 100) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $comment->user->name ?? 'Unknown' }} • {{ $comment->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">No comments yet</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
                    </div>
                    <div class="p-6">
                        @if($recentActivity->count() > 0)
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    @foreach($recentActivity as $activity)
                                        <li>
                                            <div class="relative pb-8">
                                                @if(!$loop->last)
                                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex space-x-3">
                                                    <div>
                                                        <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                        <div>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                <span class="font-medium text-gray-900 dark:text-white">{{ $activity->user->name ?? 'System' }}</span>
                                                                {{ $activity->event }}
                                                                <span class="font-medium text-gray-900 dark:text-white">{{ class_basename($activity->auditable_type) }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                            {{ $activity->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">No recent activity</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
