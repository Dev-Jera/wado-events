@extends('layouts.app')

@section('content')
    <section class="checkout-page">
        @php
            $paymentNotice = session('payment_notice');
        @endphp

        @if (is_array($paymentNotice) && !empty($paymentNotice['message']))
            <div class="checkout-popup" id="checkout-popup" role="status" aria-live="polite" aria-atomic="true">
                <div class="checkout-popup-card checkout-popup-{{ $paymentNotice['type'] ?? 'info' }}">
                    <p class="checkout-popup-title">
                        {{ ($paymentNotice['type'] ?? 'info') === 'success' ? 'Payment Request Sent' : 'Payment Update' }}
                    </p>
                    <p class="checkout-popup-message">{{ $paymentNotice['message'] }}</p>
                    <button type="button" class="checkout-popup-close" id="checkout-popup-close">OK</button>
                </div>
            </div>
        @endif

        <div class="checkout-shell">

            {{-- LEFT: Event info + order summary --}}
            <div class="checkout-left">

                <div class="event-banner" style="background-image: linear-gradient(rgba(7,16,28,0.3), rgba(7,16,28,0.85)), url('{{ asset(ltrim((string) $event->image_url, '/')) }}')">
                    <p class="banner-cat">{{ $event->category?->name ?? 'Event' }}</p>
                    <p class="banner-title">{{ $event->title }}</p>
                    <div class="banner-meta">
                        <span class="banner-meta-item">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M5 2v2M11 2v2M2 7h12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                            {{ $event->starts_at->format('l, d M Y · h:i A') }}
                        </span>
                        <span class="banner-meta-item">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M8 1.5C5.515 1.5 3.5 3.515 3.5 6c0 3.75 4.5 8.5 4.5 8.5s4.5-4.75 4.5-8.5c0-2.485-2.015-4.5-4.5-4.5z" stroke="currentColor" stroke-width="1.2"/><circle cx="8" cy="6" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
                            {{ $event->venue }}, {{ $event->city }}
                        </span>
                    </div>
                </div>

                <div class="order-summary">
                    <p class="os-title">Order summary</p>
                    <div class="os-row">
                        <span class="os-label">Ticket type</span>
                        <span class="os-val" id="summary-category">—</span>
                    </div>
                    <div class="os-row">
                        <span class="os-label">Unit price</span>
                        <span class="os-val" id="summary-price">—</span>
                    </div>
                    <div class="os-row">
                        <span class="os-label">Quantity</span>
                        <span class="os-val" id="summary-qty">1</span>
                    </div>
                    <div class="os-total">
                        <span class="os-total-label">Total</span>
                        <span class="os-total-val" id="summary-total">—</span>
                    </div>
                </div>

                <a href="{{ route('events.show', $event) }}" class="checkout-back">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M10 3L6 8l4 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Back to event
                </a>

            </div>

            {{-- RIGHT: Form --}}
            <form method="POST" action="{{ route('checkout.store', $event) }}" class="checkout-right" id="checkout-form">
                @csrf
                <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', $idempotencyKey) }}">
                <input type="hidden" name="payment_provider" id="payment_provider_input" value="{{ old('payment_provider', $selectedPaymentProvider) }}">

                @php $allFree = $event->is_free || $event->ticketCategories->every(fn($c) => (float) $c->price <= 0); @endphp

                <p class="form-title">{{ $allFree ? 'Reserve your spot' : 'Payment details' }}</p>
                <p class="form-sub">Fill in your details to complete your booking</p>

                @guest
                    <div class="guest-warning" role="alert">
                        <p class="guest-warning-title">Guest checkout is the default</p>
                        <p class="guest-warning-text">
                            Your ticket will be delivered to email (and payment updates to your phone). You will not be able to track tickets in My Tickets unless you choose to create an account now.
                        </p>
                    </div>
                @endguest

                <label class="field">
                    <span>Ticket category</span>
                    <select name="ticket_category_id" id="ticket_category_id" required>
                        @foreach ($event->ticketCategories as $ticketCategory)
                            <option
                                value="{{ $ticketCategory->id }}"
                                data-price="{{ (float) $ticketCategory->price }}"
                                data-label="{{ $ticketCategory->name }}"
                                data-price-label="{{ (float) $ticketCategory->price <= 0 ? 'Free' : 'UGX '.number_format((float) $ticketCategory->price, 0) }}"
                                @selected(old('ticket_category_id', $selectedCategoryId) == $ticketCategory->id)
                            >
                                {{ $ticketCategory->name }} · {{ (float) $ticketCategory->price <= 0 ? 'Free' : 'UGX '.number_format((float) $ticketCategory->price, 0) }} · {{ $ticketCategory->tickets_remaining }} left
                            </option>
                        @endforeach
                    </select>
                    @error('ticket_category_id') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Quantity</span>
                    <input type="number" name="quantity" id="quantity" min="1" max="20" value="{{ old('quantity', $selectedQuantity ?? 1) }}" required>
                    @error('quantity') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Full name</span>
                    <input type="text" name="holder_name" value="{{ old('holder_name', $holderName) }}" placeholder="e.g. John Smith" required autocomplete="name">
                    @error('holder_name') <small>{{ $message }}</small> @enderror
                </label>

                @guest
                    <label class="field">
                        <span>Email for ticket delivery</span>
                        <input type="text" name="email" value="{{ old('email', $email ?? '') }}" placeholder="e.g. you@example.com" required autocomplete="email">
                        @error('email') <small>{{ $message }}</small> @enderror
                    </label>

                    <label class="account-opt-in" for="create_account">
                        <input
                            type="checkbox"
                            name="create_account"
                            id="create_account"
                            value="1"
                            @checked(old('create_account', $createAccount ?? false))
                        >
                        <span>Create account to track tickets</span>
                    </label>

                    <div id="account_password_fields" class="password-block" style="display: none;">
                        <label class="field">
                            <span>Password</span>
                            <input type="password" name="password" minlength="8" autocomplete="new-password" placeholder="At least 8 characters">
                            @error('password') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="field">
                            <span>Confirm password</span>
                            <input type="password" name="password_confirmation" minlength="8" autocomplete="new-password" placeholder="Repeat password">
                        </label>
                    </div>
                @endguest

                <div class="field-divider"></div>

                <div id="payment-fields" style="{{ $allFree ? 'display:none;' : '' }}">
                    <div class="field" style="margin-bottom:14px;">
                        <span class="field-span">Payment provider</span>
                        <div class="provider-grid">
                            <button type="button" class="provider-btn {{ old('payment_provider', $selectedPaymentProvider) === 'mtn' ? 'is-selected' : '' }}" data-provider="mtn">
                                <div class="provider-icon mtn-icon">M</div>
                                <span class="provider-name">MTN MoMo</span>
                            </button>
                            <button type="button" class="provider-btn {{ old('payment_provider', $selectedPaymentProvider) === 'airtel' ? 'is-selected' : '' }}" data-provider="airtel">
                                <div class="provider-icon airtel-icon">A</div>
                                <span class="provider-name">Airtel Money</span>
                            </button>
                        </div>
                        @error('payment_provider') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    <label class="field">
                        <span>Mobile money number</span>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $phoneNumber) }}" placeholder="e.g. 2567XXXXXXXX">
                        @error('phone_number') <small>{{ $message }}</small> @enderror
                    </label>

                    <div class="field-divider" style="margin-top:14px;"></div>
                </div>

                <button type="submit" class="checkout-btn" id="checkout-submit-btn">
                    {{ $allFree ? 'Get Ticket →' : 'Initiate payment →' }}
                </button>

                <p class="secure-note" id="secure-note" style="{{ $allFree ? 'display:none;' : '' }}">
                    <svg width="11" height="11" viewBox="0 0 16 16" fill="none"><path d="M8 1L3 3.5v4C3 10.5 5.5 13.5 8 15c2.5-1.5 5-4.5 5-7.5v-4L8 1z" stroke="currentColor" stroke-width="1.2"/></svg>
                    Secured · Your payment is encrypted
                </p>
            </form>

        </div>
    </section>

    <style>
        :root {
            --brand-blue: #1a73e8;
            --brand-blue-dark: #1558c0;
            --brand-red: #e8241a;
            --brand-red-dark: #c01e15;
        }

        .checkout-page {
            min-height: 100vh;
            padding: 8.5rem 1rem 4rem;
            background: #07101c;
        }

        .checkout-shell {
            width: min(960px, calc(100% - 2rem));
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            align-items: start;
        }

        .checkout-popup {
            position: fixed;
            inset: 0;
            z-index: 60;
            background: rgba(2, 8, 20, 0.65);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .checkout-popup-card {
            width: min(520px, calc(100% - 1rem));
            border-radius: 16px;
            padding: 1rem 1rem 0.9rem;
            border: 1px solid #2b4771;
            background: #10223a;
            box-shadow: 0 18px 42px rgba(0, 0, 0, 0.35);
        }

        .checkout-popup-success {
            border-color: #2f855a;
            background: #0f2b23;
        }

        .checkout-popup-error {
            border-color: #a03b41;
            background: #33161a;
        }

        .checkout-popup-title {
            margin: 0;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
        }

        .checkout-popup-message {
            margin: 0.55rem 0 0;
            color: #d5e2f5;
            font-size: 0.92rem;
            line-height: 1.45;
        }

        .checkout-popup-close {
            margin-top: 0.9rem;
            border: none;
            border-radius: 10px;
            background: #1a73e8;
            color: #ffffff;
            font-size: 0.86rem;
            font-weight: 700;
            padding: 0.58rem 1rem;
            cursor: pointer;
        }

        /* ── Left ── */
        .checkout-left { display: flex; flex-direction: column; gap: 12px; }

        .event-banner {
            border-radius: 16px; overflow: hidden;
            height: 210px;
            background-size: cover; background-position: center;
            display: flex; flex-direction: column; justify-content: flex-end;
            padding: 18px 20px;
        }

        .banner-cat {
            font-size: 10px; font-weight: 700; letter-spacing: 0.12em;
            text-transform: uppercase; color: var(--brand-blue); margin-bottom: 4px;
        }

        .banner-title {
            font-size: 1.25rem; font-weight: 700; color: #fff;
            line-height: 1.2; margin-bottom: 10px;
        }

        .banner-meta { display: flex; flex-direction: column; gap: 4px; }

        .banner-meta-item {
            font-size: 11px; color: #8a9ab8;
            display: flex; align-items: center; gap: 5px;
        }

        .banner-meta-item svg { opacity: 0.6; flex-shrink: 0; }

        .order-summary {
            background: #111d2e; border: 0.5px solid #1e3050;
            border-radius: 16px; padding: 16px 18px;
        }

        .os-title {
            font-size: 10px; font-weight: 700; letter-spacing: 0.12em;
            text-transform: uppercase; color: var(--brand-blue); margin-bottom: 10px;
        }

        .os-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 8px 0; border-bottom: 0.5px solid #1a2d45;
        }

        .os-row:last-of-type { border-bottom: none; }
        .os-label { font-size: 12px; color: #5a7a9a; }
        .os-val { font-size: 13px; font-weight: 600; color: #fff; }

        .os-total {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 10px; padding-top: 10px; border-top: 1px solid #1e3050;
        }

        .os-total-label { font-size: 13px; font-weight: 700; color: #fff; }
        .os-total-val { font-size: 16px; font-weight: 700; color: var(--brand-blue); }

        .checkout-back {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; color: #4a6480; text-decoration: none;
            transition: color 0.15s;
        }

        .checkout-back:hover { color: var(--brand-blue); }

        /* ── Right ── */
        .checkout-right {
            background: #111d2e; border: 0.5px solid #1e3050;
            border-radius: 16px; padding: 20px;
            display: flex; flex-direction: column; gap: 14px;
        }

        .form-title { font-size: 15px; font-weight: 700; color: #fff; }
        .form-sub { font-size: 12px; color: #5a7a9a; margin-top: -8px; }

        .field { display: flex; flex-direction: column; gap: 6px; }

        .field span, .field-span {
            font-size: 11px; font-weight: 600; color: #c0cfe8; letter-spacing: 0.04em;
        }

        .field select,
        .field input[type="text"],
        .field input[type="number"] {
            width: 100%; height: 42px;
            border: 1px solid #1e3050; border-radius: 10px;
            background: #0d1929; color: #fff;
            padding: 0 12px; font-size: 13px;
            transition: border-color 0.15s, box-shadow 0.15s;
            appearance: auto;
        }

        .field select:focus,
        .field input:focus {
            outline: none;
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 3px rgba(26,115,232,0.1);
        }

        .field select option { background: #0d1929; color: #fff; }
        .field small, .field-error { font-size: 11px; color: var(--brand-red); font-weight: 500; }

        .field-divider { height: 0.5px; background: #1e3050; }

        .guest-warning {
            border: 1px solid #2d4d76;
            background: #0f2036;
            border-radius: 12px;
            padding: 0.7rem 0.8rem;
            display: grid;
            gap: 0.25rem;
        }

        .guest-warning-title {
            margin: 0;
            font-size: 0.8rem;
            font-weight: 700;
            color: #dcecff;
        }

        .guest-warning-text {
            margin: 0;
            font-size: 0.72rem;
            line-height: 1.45;
            color: #9eb7d5;
        }

        .account-opt-in {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.76rem;
            color: #dcecff;
            font-weight: 600;
            cursor: pointer;
        }

        .account-opt-in input {
            width: 15px;
            height: 15px;
            accent-color: var(--brand-blue);
        }

        .password-block {
            border: 1px solid #1e3050;
            border-radius: 10px;
            padding: 0.7rem;
            display: grid;
            gap: 0.65rem;
            background: #0d1929;
        }

        .provider-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 2px; }

        .provider-btn {
            border: 1px solid #1e3050; border-radius: 10px;
            background: #0d1929; padding: 10px 12px;
            display: flex; align-items: center; gap: 8px;
            cursor: pointer; transition: border-color 0.15s, background 0.15s;
        }

        .provider-btn:hover { border-color: var(--brand-blue); }

        .provider-btn.is-selected {
            border-color: var(--brand-blue);
            background: rgba(26,115,232,0.1);
        }

        .provider-icon {
            width: 28px; height: 28px; border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 800; flex-shrink: 0;
        }

        .mtn-icon { background: #ffcc00; color: #111; }
        .airtel-icon { background: var(--brand-red); color: #fff; }

        .provider-name { font-size: 12px; font-weight: 600; color: #c0cfe8; }
        .provider-btn.is-selected .provider-name { color: #fff; }

        .checkout-btn {
            height: 48px; border: none; border-radius: 12px;
            background: var(--brand-red); color: #fff;
            font-size: 14px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.15s;
        }

        .checkout-btn:hover { background: var(--brand-red-dark); }

        .secure-note {
            text-align: center; font-size: 11px; color: #2a4060;
            display: flex; align-items: center; justify-content: center; gap: 4px;
        }

        @media (max-width: 760px) {
            .checkout-shell { grid-template-columns: 1fr; }
            .event-banner { height: 170px; }
        }
    </style>

    <script>
        (() => {
            const categorySelect = document.getElementById('ticket_category_id');
            const quantityInput = document.getElementById('quantity');
            const summaryCategory = document.getElementById('summary-category');
            const summaryPrice = document.getElementById('summary-price');
            const summaryQty = document.getElementById('summary-qty');
            const summaryTotal = document.getElementById('summary-total');
            const providerInput = document.getElementById('payment_provider_input');
            const providerBtns = document.querySelectorAll('.provider-btn');
            const popup = document.getElementById('checkout-popup');
            const popupClose = document.getElementById('checkout-popup-close');
            const createAccountCheckbox = document.getElementById('create_account');
            const passwordFields = document.getElementById('account_password_fields');

            const formatUgx = (n) => n <= 0 ? 'Free' : 'UGX ' + Math.round(n).toLocaleString('en-US');
            const paymentFields = document.getElementById('payment-fields');
            const secureNote = document.getElementById('secure-note');
            const submitBtn = document.getElementById('checkout-submit-btn');

            const updateSummary = () => {
                const selected = categorySelect?.options[categorySelect.selectedIndex];
                const price = parseFloat(selected?.dataset.price || 0);
                const label = selected?.dataset.label || '—';
                const priceLabel = selected?.dataset.priceLabel || '—';
                const qty = parseInt(quantityInput?.value || 1, 10);
                const isFree = price <= 0;

                if (summaryCategory) summaryCategory.textContent = label;
                if (summaryPrice) summaryPrice.textContent = priceLabel;
                if (summaryQty) summaryQty.textContent = qty;
                if (summaryTotal) summaryTotal.textContent = isFree ? 'Free' : formatUgx(price * qty);

                if (paymentFields) paymentFields.style.display = isFree ? 'none' : '';
                if (secureNote) secureNote.style.display = isFree ? 'none' : '';
                if (submitBtn) submitBtn.textContent = isFree ? 'Get Ticket →' : 'Initiate payment →';
            };

            if (categorySelect) categorySelect.addEventListener('change', updateSummary);
            if (quantityInput) quantityInput.addEventListener('input', updateSummary);

            providerBtns.forEach((btn) => {
                btn.addEventListener('click', () => {
                    providerBtns.forEach((b) => b.classList.remove('is-selected'));
                    btn.classList.add('is-selected');
                    if (providerInput) providerInput.value = btn.dataset.provider;
                });
            });

            const syncAccountFields = () => {
                if (!createAccountCheckbox || !passwordFields) return;
                passwordFields.style.display = createAccountCheckbox.checked ? 'grid' : 'none';
            };

            if (createAccountCheckbox) {
                createAccountCheckbox.addEventListener('change', syncAccountFields);
                syncAccountFields();
            }

            if (popup && popupClose) {
                popupClose.addEventListener('click', () => {
                    popup.style.display = 'none';
                });

                popup.addEventListener('click', (event) => {
                    if (event.target === popup) {
                        popup.style.display = 'none';
                    }
                });
            }

            updateSummary();
        })();
    </script>
@endsection