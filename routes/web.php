<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('events.index');
    })->name('dashboard');

    // Event routes
    Route::get('/events', App\Livewire\Events\Index::class)->name('events.index');
    Route::get('/events/create', App\Livewire\Events\Form::class)->name('events.create');
    Route::get('/events/{eventId}', App\Livewire\Events\Dashboard::class)->name('events.dashboard');
    Route::get('/events/{eventId}/edit', App\Livewire\Events\Form::class)->name('events.edit');
    Route::get('/events/{eventId}/users', App\Livewire\Events\ManageUsers::class)->name('events.users');

    // Role management routes (super admin only)
    Route::get('/roles', App\Livewire\Roles\Index::class)->name('roles.index');
    Route::get('/roles/create', App\Livewire\Roles\Form::class)->name('roles.create');
    Route::get('/roles/{roleId}/edit', App\Livewire\Roles\Form::class)->name('roles.edit');

    // Audit logs route
    Route::get('/audit-logs', App\Livewire\AuditLogs\Index::class)->name('audit-logs.index');

    // Activity feed route
    Route::get('/activity-feed', App\Livewire\ActivityFeed\Index::class)->name('activity-feed.index');

    // Session routes
    Route::get('/events/{eventId}/sessions', App\Livewire\Sessions\Index::class)->name('events.sessions.index');
    Route::get('/events/{eventId}/sessions/create', App\Livewire\Sessions\Form::class)->name('events.sessions.create');
    Route::get('/events/{eventId}/sessions/{sessionId}/edit', App\Livewire\Sessions\Form::class)->name('events.sessions.edit');

    // Custom field routes
    Route::get('/events/{eventId}/custom-fields', App\Livewire\CustomFields\Index::class)->name('custom-fields.index');
    Route::get('/events/{eventId}/custom-fields/create', App\Livewire\CustomFields\Form::class)->name('custom-fields.create');
    Route::get('/events/{eventId}/custom-fields/{fieldId}/edit', App\Livewire\CustomFields\Form::class)->name('custom-fields.edit');

    // Segment routes
    Route::get('/sessions/{sessionId}/segments', App\Livewire\Segments\Index::class)->name('sessions.segments.index');
    Route::get('/sessions/{sessionId}/segments/create', App\Livewire\Segments\Form::class)->name('sessions.segments.create');
    Route::get('/sessions/{sessionId}/segments/{segmentId}/edit', App\Livewire\Segments\Form::class)->name('sessions.segments.edit');

    // Cue routes
    Route::get('/segments/{segmentId}/cues', App\Livewire\Cues\Index::class)->name('segments.cues.index');
    Route::get('/segments/{segmentId}/cues/create', App\Livewire\Cues\Form::class)->name('segments.cues.create');
    Route::get('/segments/{segmentId}/cues/{cueId}/edit', App\Livewire\Cues\Form::class)->name('segments.cues.edit');

    // Content management routes
    Route::get('/events/{eventId}/content', App\Livewire\Content\Index::class)->name('events.content.index');

    // Speaker routes
    Route::get('/events/{eventId}/speakers', App\Livewire\Speakers\Index::class)->name('events.speakers.index');
    Route::get('/events/{eventId}/speakers/create', App\Livewire\Speakers\Form::class)->name('events.speakers.create');
    Route::get('/events/{eventId}/speakers/{speakerId}/edit', App\Livewire\Speakers\Form::class)->name('events.speakers.edit');
    Route::get('/events/{eventId}/speakers/{speakerId}', App\Livewire\Speakers\Show::class)->name('events.speakers.show');

    // Contact routes
    Route::get('/events/{eventId}/contacts', App\Livewire\Contacts\Index::class)->name('events.contacts.index');
    Route::get('/events/{eventId}/contacts/create', App\Livewire\Contacts\Form::class)->name('events.contacts.create');
    Route::get('/events/{eventId}/contacts/{contactId}/edit', App\Livewire\Contacts\Form::class)->name('events.contacts.edit');
    Route::get('/events/{eventId}/contacts/{contactId}', App\Livewire\Contacts\Show::class)->name('events.contacts.show');

    // Event-specific Tags and Audit routes
    // TODO: Create Tags\Index component
    Route::get('/events/{eventId}/tags', App\Livewire\Tags\Index::class)->name('events.tags.index');
    // TODO: Update AuditLogs\Index to accept eventId parameter
    // Route::get('/events/{eventId}/audit-logs', App\Livewire\AuditLogs\Index::class)->name('events.audit-logs.index');

    // Show calling routes
    Route::get('/events/{eventId}/show-call', App\Livewire\ShowCall\Index::class)->name('show-call.index');
    Route::get('/events/{eventId}/show-call/{sessionId}', App\Livewire\ShowCall\Index::class)->name('show-call.session');
    
    // Run of Show routes
    Route::get('/sessions/{sessionId}/run-of-show', App\Livewire\RunOfShow\Index::class)->name('sessions.run-of-show');
});
