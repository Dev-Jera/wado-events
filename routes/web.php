<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GateBatchController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\EventBookmarkController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GatePortalController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketDismissController;
use App\Http\Controllers\TicketVerificationController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class)->name('health');

Route::get('/', HomeController::class)->name('home');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send')->middleware('throttle:5,1');
Route::get('/events', [EventController::class, 'index'])->middleware('throttle:60,1')->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->middleware('throttle:60,1')->name('events.show');

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware(['throttle:10,1', \App\Http\Middleware\VerifyTurnstile::class])->name('register.store');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Email verification — notice and resend require auth; verify link uses signed middleware only
Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');
Route::post('/email/resend-verification', [AuthController::class, 'resendVerification'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/my-tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/my-tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/my-tickets/{ticket}/download', [TicketController::class, 'download'])->name('tickets.download');
    Route::get('/my-tickets/{ticket}/pdf', [TicketController::class, 'downloadPdf'])->name('tickets.pdf');
    Route::post('/my-tickets/{ticket}/refund-request', [TicketController::class, 'requestRefund'])->name('tickets.refund.request');
    Route::post('/my-tickets/{ticket}/dismiss', [TicketDismissController::class, 'store'])->name('tickets.dismiss');

    Route::post('/events/{event}/bookmark', [EventBookmarkController::class, 'toggle'])->name('events.bookmark');

    Route::get('/ticket-verification', [TicketVerificationController::class, 'index'])->name('tickets.verify.index');
    Route::post('/ticket-verification', [TicketVerificationController::class, 'verify'])->name('tickets.verify.store');
    Route::post('/ticket-verification/scan', [TicketVerificationController::class, 'scanJson'])->middleware('throttle:60,1')->name('tickets.verify.scan-json');
    Route::get('/ticket-verification/export', [TicketVerificationController::class, 'export'])->name('tickets.verify.export');

    // Payment Monitor → Filament admin
    Route::get('/admin/payments', fn () => redirect('/dashboard/payment-transactions'))->name('payments.admin.index');
    Route::post('/admin/payments/{paymentTransaction}/resend', [PaymentController::class, 'adminResend'])->name('payments.admin.resend');
    Route::post('/admin/payments/{paymentTransaction}/confirm', [PaymentController::class, 'adminConfirm'])->name('payments.admin.confirm');
    Route::post('/admin/payments/{paymentTransaction}/refund', [PaymentController::class, 'adminRefund'])->name('payments.admin.refund');

    Route::get('/gate-batches/{batchId}/download-pdf', [GateBatchController::class, 'downloadPdf'])->name('gate-batches.download-pdf');

    Route::get('/gate-portal', [GatePortalController::class, 'index'])->name('gate.portal');
    Route::post('/gate-portal/walk-in-sale', [GatePortalController::class, 'storeWalkInSale'])->name('gate.portal.walkin.store');
    Route::get('/gate-portal/walkin-requests', [GatePortalController::class, 'pendingWalkInRequests'])->name('gate.portal.walkin.requests');

    Route::get('/admin/events/{event}', [AdminEventController::class, 'show'])->name('admin.events.show');

    // Finance index → Filament admin; event detail kept for CSV export
    Route::get('/admin/finance', fn () => redirect('/dashboard/finance'))->name('admin.finance.index');
    Route::get('/admin/finance/{event}', [FinanceController::class, 'show'])->name('admin.finance.show');

    Route::get('/admin/pdf-preview/{ticket}', function (\App\Models\Ticket $ticket) {
        abort_unless(auth()->user()?->isSuperAdmin() || auth()->user()?->isAdmin(), 403);

        $ticket->load(['event.category', 'ticketCategory']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.tickets.pdf_compact', [
            'ticket'        => $ticket,
            'qrCodeDataUri' => app(\App\Http\Controllers\TicketController::class)->buildPngQrDataUri($ticket),
            'eventImageUri' => app(\App\Http\Controllers\TicketController::class)->buildEventImageDataUri($ticket),
        ]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false, 'defaultFont' => 'sans-serif']);

        return $pdf->stream('wado-ticket-preview.pdf');
    })->name('admin.pdf.preview');

    Route::get('/admin/email-preview/{ticket}', function (\App\Models\Ticket $ticket) {
        abort_unless(auth()->user()?->isSuperAdmin() || auth()->user()?->isAdmin(), 403);

        $ticket->loadMissing(['event', 'user', 'ticketCategory']);
        $ticketUrl = route('tickets.show', $ticket);

        $qrCodeDataUri = null;
        try {
            $qrPayload = json_encode(['v' => 2, 'code' => (string) $ticket->ticket_code, 'event_id' => (int) $ticket->event_id], JSON_UNESCAPED_SLASHES);
            $result = (new \Endroid\QrCode\Builder\Builder(
                writer: new \Endroid\QrCode\Writer\PngWriter(),
                writerOptions: [],
                data: $qrPayload,
                encoding: new \Endroid\QrCode\Encoding\Encoding('UTF-8'),
                errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::Medium,
                size: 300,
                margin: 10,
                roundBlockSizeMode: \Endroid\QrCode\RoundBlockSizeMode::Margin,
            ))->build();
            $qrCodeDataUri = 'data:image/png;base64,' . base64_encode($result->getString());
        } catch (\Throwable) {}

        $rawImage = $ticket->event?->image_url;
        $eventImageUrl = $rawImage
            ? (str_starts_with($rawImage, 'http') ? $rawImage : rtrim(config('app.url'), '/') . '/' . ltrim($rawImage, '/'))
            : null;

        return view('emails.tickets.confirmed', [
            'ticket'        => $ticket,
            'payment'       => $ticket->paymentTransaction,
            'ticketUrl'     => $ticketUrl,
            'qrCodeDataUri' => $qrCodeDataUri,
            'eventImageUrl' => $eventImageUrl,
        ]);
    })->name('admin.email.preview');
});

Route::get('/events/{event}/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/events/{event}/checkout', [CheckoutController::class, 'store'])->middleware(['throttle:30,1', \App\Http\Middleware\VerifyTurnstile::class])->name('checkout.store');
Route::get('/checkout/confirmed', [CheckoutController::class, 'confirmed'])->name('checkout.confirmed');
Route::post('/promo-codes/validate', [\App\Http\Controllers\PromoCodeController::class, 'validate'])->middleware('throttle:20,1')->name('promo.validate');


// MarzPay Webhook - Must bypass CSRF protection
Route::post('/payments/marzepay/webhook', PaymentWebhookController::class)
    ->withoutMiddleware(VerifyCsrfToken::class)
    ->name('payments.webhook.marzepay');

require __DIR__.'/ticket-packages.php';

Route::middleware(['auth', 'is_super_admin'])->prefix('dashboard')->group(function () {
    // Content management is now a Filament page at /dashboard/content-management-page
    Route::get('/content-management', fn () => redirect('/dashboard/content-management-page'))->name('content-management.index');
});
