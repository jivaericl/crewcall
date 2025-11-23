<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Calendar API routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/events/{eventId}/calendar/events', [\App\Http\Controllers\Api\CalendarController::class, 'getEvents']);
    Route::put('/events/{eventId}/calendar/{calendarItemId}', [\App\Http\Controllers\Api\CalendarController::class, 'updateEvent']);
});
