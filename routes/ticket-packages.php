<?php

use App\Http\Controllers\TicketPackageController;
use Illuminate\Support\Facades\Route;

Route::get('/ticket-packages', [TicketPackageController::class, 'index'])->name('ticket-packages.index');
Route::post('/ticket-packages/enquire', [TicketPackageController::class, 'enquire'])->middleware('throttle:5,1')->name('ticket-packages.enquire');