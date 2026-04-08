<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GatePortalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketVerificationController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/my-tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/my-tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

    Route::get('/ticket-verification', [TicketVerificationController::class, 'index'])->name('tickets.verify.index');
    Route::post('/ticket-verification', [TicketVerificationController::class, 'verify'])->name('tickets.verify.store');
    Route::get('/ticket-verification/export', [TicketVerificationController::class, 'export'])->name('tickets.verify.export');

    Route::get('/payments/{paymentTransaction}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/admin/payments', [PaymentController::class, 'adminIndex'])->name('payments.admin.index');
    Route::post('/admin/payments/{paymentTransaction}/resend', [PaymentController::class, 'adminResend'])->name('payments.admin.resend');

    Route::get('/gate-portal', [GatePortalController::class, 'index'])->name('gate.portal');

    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users/agents', [UserManagementController::class, 'storeAgent'])->name('admin.users.storeAgent');
    Route::post('/admin/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('admin.users.updateRole');
});

Route::get('/events/{event}/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/events/{event}/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::post('/payments/marzepay/webhook', PaymentWebhookController::class)
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('payments.webhook.marzepay');
