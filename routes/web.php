<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\EventBookmarkController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GatePortalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketDismissController;
use App\Http\Controllers\TicketVerificationController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/my-tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/my-tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/my-tickets/{ticket}/download', [TicketController::class, 'download'])->name('tickets.download');
    Route::get('/my-tickets/{ticket}/pdf', [TicketController::class, 'downloadPdf'])->name('tickets.pdf');
    Route::post('/my-tickets/{ticket}/refund-request', [TicketController::class, 'requestRefund'])->name('tickets.refund.request');
    Route::post('/my-tickets/{ticket}/dismiss', [TicketDismissController::class, 'store'])->name('tickets.dismiss');

    Route::post('/events/{event}/bookmark', [EventBookmarkController::class, 'toggle'])->name('events.bookmark');

    Route::get('/ticket-verification', [TicketVerificationController::class, 'index'])->name('tickets.verify.index');
    Route::post('/ticket-verification', [TicketVerificationController::class, 'verify'])->name('tickets.verify.store');
    Route::post('/ticket-verification/scan', [TicketVerificationController::class, 'scanJson'])->name('tickets.verify.scan-json');
    Route::get('/ticket-verification/export', [TicketVerificationController::class, 'export'])->name('tickets.verify.export');

    Route::get('/admin/payments', [PaymentController::class, 'adminIndex'])->name('payments.admin.index');
    Route::post('/admin/payments/{paymentTransaction}/resend', [PaymentController::class, 'adminResend'])->name('payments.admin.resend');
    Route::post('/admin/payments/{paymentTransaction}/confirm', [PaymentController::class, 'adminConfirm'])->name('payments.admin.confirm');
    Route::post('/admin/payments/{paymentTransaction}/refund', [PaymentController::class, 'adminRefund'])->name('payments.admin.refund');

    Route::get('/gate-portal', [GatePortalController::class, 'index'])->name('gate.portal');
    Route::post('/gate-portal/walk-in-sale', [GatePortalController::class, 'storeWalkInSale'])->name('gate.portal.walkin.store');

    Route::get('/admin/events/{event}', [AdminEventController::class, 'show'])->name('admin.events.show');

    Route::get('/admin/finance', [FinanceController::class, 'index'])->name('admin.finance.index');
    Route::get('/admin/finance/{event}', [FinanceController::class, 'show'])->name('admin.finance.show');
});

Route::get('/events/{event}/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/events/{event}/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// TEMPORARY: outbound IP check — remove after use
Route::get('/outbound-ip', function () {
    $ip = file_get_contents('https://api.ipify.org');
    return response("Railway outbound IP: {$ip}");
});

// MarzPay Webhook - Must bypass CSRF protection
Route::post('/payments/marzepay/webhook', PaymentWebhookController::class)
    ->withoutMiddleware(VerifyCsrfToken::class)
    ->name('payments.webhook.marzepay');
