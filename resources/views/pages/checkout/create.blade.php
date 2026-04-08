@extends('layouts.app')

@section('content')
    <section class="checkout-page">
        <div class="checkout-shell">
            <div class="checkout-summary">
                <p class="checkout-kicker">Secure checkout</p>
                <h1>{{ $event->title }}</h1>
                <p>{{ $event->venue }}, {{ $event->city }} · {{ $event->starts_at->format('d M Y, h:i A') }}</p>
                <a href="{{ route('events.show', $event) }}" class="checkout-back">Back to event</a>
            </div>

            <form method="POST" action="{{ route('checkout.store', $event) }}" class="checkout-card">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', $idempotencyKey) }}">
                <h2>Choose your ticket</h2>

                <label>
                    <span>Ticket category</span>
                    <select name="ticket_category_id" required>
                        @foreach ($event->ticketCategories as $ticketCategory)
                            <option value="{{ $ticketCategory->id }}" @selected(old('ticket_category_id', $selectedCategoryId) == $ticketCategory->id)>
                                {{ $ticketCategory->name }} · {{ (float) $ticketCategory->price <= 0 ? 'Free' : 'UGX '.number_format((float) $ticketCategory->price, 0) }} · {{ $ticketCategory->tickets_remaining }} left
                            </option>
                        @endforeach
                    </select>
                    @error('ticket_category_id') <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Quantity</span>
                    <input type="number" name="quantity" min="1" max="20" value="{{ old('quantity', $selectedQuantity) }}" required>
                    @error('quantity') <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Payment provider</span>
                    <select name="payment_provider">
                        <option value="">Select provider</option>
                        <option value="mtn" @selected(old('payment_provider', $selectedPaymentProvider) === 'mtn')>MTN MoMo</option>
                        <option value="airtel" @selected(old('payment_provider', $selectedPaymentProvider) === 'airtel')>Airtel Money</option>
                    </select>
                    @error('payment_provider') <small>{{ $message }}</small> @enderror
                </label>

                <label>
                    <span>Mobile money number</span>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $phoneNumber) }}" placeholder="e.g. 2567XXXXXXXX">
                    @error('phone_number') <small>{{ $message }}</small> @enderror
                </label>

                <button type="submit" class="checkout-btn">Initiate payment</button>
            </form>
        </div>
    </section>

    <style>
        .checkout-page { min-height: 100vh; padding: 8.5rem 1rem 4rem; background: #ffffff; }
        .checkout-shell { width: min(1080px, calc(100% - 2rem)); margin: 0 auto; display: grid; grid-template-columns: 1fr minmax(320px, 420px); gap: 1.2rem; }
        .checkout-summary { border-radius: 28px; padding: 1.8rem; background: #ffffff; border: 1px solid #d8e7f5; color: #123d63; box-shadow: 0 14px 34px rgba(11, 126, 208, 0.08); }
        .checkout-card { border-radius: 28px; padding: 1.8rem; background: #0b7ed0; border: 1px solid #0b7ed0; display: grid; gap: 1rem; color: #ffffff; }
        .checkout-kicker { margin: 0; color: #0b7ed0; text-transform: uppercase; font-size: 0.78rem; letter-spacing: 0.13em; font-weight: 700; }
        .checkout-summary h1 { margin: 0.7rem 0 0; font-size: clamp(2rem, 4vw, 3.3rem); }
        .checkout-summary p { color: #4b6377; line-height: 1.7; }
        .checkout-back { color: #c21f32; text-decoration: none; font-weight: 700; }
        .checkout-card h2 { margin: 0; }
        .checkout-card label { display: grid; gap: 0.45rem; font-weight: 600; color: #ffffff; }
        .checkout-card select, .checkout-card input { width: 100%; border-radius: 16px; border: 1px solid #c7dff1; background: #ffffff; color: #123d63; padding: 0.95rem 1rem; }
        .checkout-card small { color: #ffe0e0; }
        .checkout-btn { border: 0; border-radius: 999px; padding: 1rem 1.3rem; font-weight: 700; color: #fff; background: linear-gradient(90deg, #ef4444, #b91c1c); cursor: pointer; }
        @media (max-width: 900px) { .checkout-shell { grid-template-columns: 1fr; } }
    </style>
@endsection
