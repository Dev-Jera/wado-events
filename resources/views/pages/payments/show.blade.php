@extends('layouts.app')

@section('content')
    <section class="payment-status-page">
        <div class="payment-status-shell">
            <h1>Payment Status</h1>
            <p class="meta">Reference: {{ $payment->idempotency_key }}</p>

            @php
                $status = strtoupper((string) $payment->status);
                $badgeClass = match ($status) {
                    'CONFIRMED' => 'ok',
                    'FAILED' => 'bad',
                    'REFUNDED' => 'warn',
                    default => 'pending',
                };
            @endphp

            <article class="status-card {{ $badgeClass }}">
                <strong>{{ $status }}</strong>
                <p>Provider: {{ strtoupper((string) $payment->payment_provider) }} · Amount: UGX {{ number_format((float) $payment->total_amount, 0) }}</p>

                @if ($status === 'PENDING' || $status === 'INITIATED')
                    <p>Please complete PIN entry on your phone. This request expires in 5 minutes.</p>
                    <script>
                        setTimeout(function () {
                            window.location.reload();
                        }, 8000);
                    </script>
                @endif

                @if (!empty($payment->last_error))
                    <p role="alert" aria-live="polite">{{ $payment->last_error }}</p>
                @endif
            </article>

            <div class="details">
                <p><span>Event:</span> {{ $payment->event?->title }}</p>
                <p><span>Category:</span> {{ $payment->ticketCategory?->name }}</p>
                <p><span>Quantity:</span> {{ $payment->quantity }}</p>
                <p><span>Phone:</span> {{ $payment->phone_number ?: 'N/A' }}</p>
            </div>

            <div class="actions">
                @if ($payment->ticket_id)
                    <a href="{{ route('tickets.show', $payment->ticket_id) }}" class="btn">Open ticket</a>
                @endif
                <a href="{{ route('checkout.create', $payment->event_id) }}" class="btn ghost">Back to checkout</a>
                <a href="{{ route('events.show', $payment->event_id) }}" class="btn ghost">Back to event</a>
            </div>
        </div>
    </section>

    <style>
        .payment-status-page { min-height: 100vh; padding: 8rem 1rem 3rem; background: #f3f7fd; }
        .payment-status-shell { width: min(760px, calc(100% - 2rem)); margin: 0 auto; background: #fff; border: 1px solid #d8e4f5; border-radius: 16px; padding: 1.4rem; }
        .payment-status-shell h1 { margin: 0; color: #16355d; }
        .meta { color: #000000; font-size: .84rem; }
        .status-card { border-radius: 12px; padding: .9rem 1rem; margin-top: .9rem; }
        .status-card.ok { background: #edf9f2; border: 1px solid #bde8cb; color: #176943; }
        .status-card.pending { background: #eff6ff; border: 1px solid #c7dcfb; color: #1c4f99; }
        .status-card.bad { background: #fff1f3; border: 1px solid #f3c5cc; color: #9e2034; }
        .status-card.warn { background: #fff6eb; border: 1px solid #ffd7a8; color: #93540d; }
        .status-card p { margin: .45rem 0 0; }
        .details { margin-top: .95rem; display: grid; gap: .4rem; }
        .details p { margin: 0; color: #254061; }
        .details span { font-weight: 700; }
        .actions { margin-top: 1rem; display: flex; gap: .6rem; flex-wrap: wrap; }
        .btn { text-decoration: none; border-radius: 9px; padding: .6rem .9rem; font-weight: 700; background: #1c67d6; color: #fff; }
        .btn.ghost { background: #eef4fe; color: #244a7e; }
    </style>
@endsection
