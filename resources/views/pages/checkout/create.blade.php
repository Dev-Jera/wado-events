@extends('layouts.app')

@section('content')
    <section class="checkout-page">
        @php
            $paymentNotice = session('payment_notice');
            $bannerPath = trim((string) $event->image_url);
            if ($bannerPath === '') {
                $bannerImageUrl = asset('images/movie.jpg');
            } elseif (str_starts_with($bannerPath, 'http://') || str_starts_with($bannerPath, 'https://')) {
                $bannerImageUrl = $bannerPath;
            } else {
                $normalizedBannerPath = ltrim($bannerPath, '/');

                if (str_starts_with($normalizedBannerPath, 'storage/') || str_starts_with($normalizedBannerPath, 'images/')) {
                    $bannerImageUrl = asset($normalizedBannerPath);
                } elseif (str_starts_with($normalizedBannerPath, 'event-images/')) {
                    $bannerImageUrl = asset('storage/' . $normalizedBannerPath);
                } else {
                    $bannerImageUrl = asset($normalizedBannerPath);
                }
            }

            $initialCategory = $event->ticketCategories->firstWhere('id', (int) old('ticket_category_id', $selectedCategoryId));
            if (! $initialCategory) {
                $initialCategory = $event->ticketCategories->first();
            }

            $initialQty = max(1, (int) old('quantity', $selectedQuantity ?? 1));
            $initialUnitPrice = (float) ($initialCategory?->price ?? 0);
            $initialSubtotal = $initialUnitPrice * $initialQty;
            $initialTotalLabel = $initialSubtotal <= 0 ? 'Free' : 'UGX '.number_format($initialSubtotal, 0);
            $initialUnitLabel = $initialUnitPrice <= 0 ? 'Free' : 'UGX '.number_format($initialUnitPrice, 0);
            $initialChargeText = $initialSubtotal <= 0
                ? 'You will be charged: Free'
                : 'You will be charged: '.$initialTotalLabel;
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

                <div class="event-banner" style="background-image: linear-gradient(rgba(20,9,12,0.35), rgba(20,9,12,0.82)), url('{{ $bannerImageUrl }}')">
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
                        <span class="os-val" id="summary-category">{{ $initialCategory?->name ?? '—' }}</span>
                    </div>
                    <div class="os-row">
                        <span class="os-label">Unit price</span>
                        <span class="os-val" id="summary-price">{{ $initialUnitLabel }}</span>
                    </div>
                    <div class="os-row">
                        <span class="os-label">Quantity</span>
                        <span class="os-val" id="summary-qty">{{ $initialQty }}</span>
                    </div>
                    <div class="os-row os-discount-row" id="summary-discount-row" style="display:none;color:#1a7a3f;">
                        <span class="os-label">Promo discount</span>
                        <span class="os-val" id="summary-discount">—</span>
                    </div>
                    <div class="os-total">
                        <span class="os-total-label">Amount to pay</span>
                        <span class="os-total-val" id="summary-total">{{ $initialTotalLabel }}</span>
                    </div>
                    <p class="os-note" id="summary-server-note">Amount is verified on the server before payment is initiated.</p>
                </div>

                <a href="{{ route('events.show', $event) }}" class="checkout-back">
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M10 3L6 8l4 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Back to event
                </a>

            </div>

            {{-- RIGHT: Form --}}
            <form method="POST" action="{{ route('checkout.store', $event) }}" class="checkout-right" id="checkout-form">
                @csrf
                {{-- Honeypot: bots fill this, humans never see it --}}
                <div style="display:none" aria-hidden="true">
                    <input type="text" name="website" value="" tabindex="-1" autocomplete="off">
                </div>
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

                @auth
                    @if (!auth()->user()->hasVerifiedEmail())
                        <div class="guest-warning" role="alert" style="border-left-color:#c0283c;">
                            <p class="guest-warning-title" style="color:#c0283c;">Email not verified</p>
                            <p class="guest-warning-text">
                                Your ticket confirmation and QR code will be sent to <strong>{{ auth()->user()->email }}</strong>. Please verify your email so delivery doesn't fail.
                                <a href="{{ route('verification.notice') }}" style="color:#c0283c;font-weight:600;white-space:nowrap;">Verify now →</a>
                            </p>
                        </div>
                    @endif
                @endauth

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
                    <input type="text" name="holder_name" value="{{ old('holder_name', $holderName) }}" placeholder="e.g. John Smith" required autocomplete="name" maxlength="255">
                    @error('holder_name') <small>{{ $message }}</small> @enderror
                </label>

                @guest
                    <label class="field">
                        <span>Email for ticket delivery</span>
                        <input type="email" name="email" value="{{ old('email', $email ?? '') }}" placeholder="e.g. you@example.com" required autocomplete="email" inputmode="email" spellcheck="false" autocapitalize="none" maxlength="255">
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
                                <div class="provider-icon mtn-icon">
                                    <img src="{{ asset('images/mtn-logo.png') }}" alt="MTN">
                                </div>
                                <span class="provider-name">MTN MoMo</span>
                            </button>
                            <button type="button" class="provider-btn {{ old('payment_provider', $selectedPaymentProvider) === 'airtel' ? 'is-selected' : '' }}" data-provider="airtel">
                                <div class="provider-icon airtel-icon">
                                    <img src="{{ asset('images/airtel-logo.png') }}" alt="Airtel">
                                </div>
                                <span class="provider-name">Airtel Money</span>
                            </button>
                        </div>
                        @error('payment_provider') <small class="field-error">{{ $message }}</small> @enderror
                    </div>

                    <label class="field">
                        <span>Mobile money number</span>
                        <input type="tel" name="phone_number" value="{{ old('phone_number', $phoneNumber) }}" placeholder="e.g. +256700000000" inputmode="numeric" pattern="^\+?[1-9]\d{7,14}$" maxlength="16" title="Use international format, for example +256700000000">
                        @error('phone_number') <small>{{ $message }}</small> @enderror
                    </label>

                    <div class="field-divider" style="margin-top:14px;"></div>
                </div>

                {{-- Promo code --}}
                <input type="hidden" name="promo_code" id="promo_code_input" value="{{ old('promo_code') }}">
                <div class="promo-section" id="promo-section">
                    <div class="promo-row">
                        <input
                            type="text"
                            id="promo_code_field"
                            class="promo-input"
                            placeholder="Have a promo code?"
                            autocomplete="off"
                            maxlength="32"
                            value="{{ old('promo_code') }}"
                        >
                        <button type="button" id="promo-apply-btn" class="promo-btn">Apply</button>
                    </div>
                    <p class="promo-feedback" id="promo-feedback" style="display:none;"></p>
                </div>

                <p class="checkout-charge-line" id="checkout-charge-line">{{ $initialChargeText }}</p>

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
            --brand-maroon: #c0283c;
            --brand-maroon-dark: #a01e2e;
            --brand-blue: #1a73e8;
            --brand-blue-dark: #1558c0;
            --bg-deep: #180e12;
            --bg-panel: #231419;
            --bg-input: #1b1015;
            --border: rgba(255,255,255,.16);
            --text-main: #f7f4f5;
            --text-sub: rgba(247,244,245,.8);
        }

        .checkout-page {
            min-height: 100vh;
            padding: 8.5rem 1rem 4rem;
            background:
                radial-gradient(ellipse 60% 44% at 0% 0%, rgba(192,40,60,.14) 0%, transparent 58%),
                radial-gradient(ellipse 42% 30% at 100% 0%, rgba(18,85,192,.08) 0%, transparent 62%),
                var(--bg-deep);
        }

        .checkout-shell {
            width: min(960px, calc(100% - 2rem));
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            align-items: start;
        }

        .checkout-popup {
            position: fixed;
            inset: 0;
            z-index: 60;
            background: rgba(10, 6, 8, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .checkout-popup-card {
            width: min(520px, calc(100% - 1rem));
            border-radius: 16px;
            padding: 1rem 1rem 0.9rem;
            border: 1px solid var(--border);
            background: #24151b;
            box-shadow: 0 18px 42px rgba(0, 0, 0, 0.35);
        }

        .checkout-popup-success {
            border-color: #3f6e57;
            background: #1e2b25;
        }

        .checkout-popup-error {
            border-color: #7f3037;
            background: #34181d;
        }

        .checkout-popup-title {
            margin: 0;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
        }

        .checkout-popup-message {
            margin: 0.55rem 0 0;
            color: var(--text-sub);
            font-size: 0.92rem;
            line-height: 1.45;
        }

        .checkout-popup-close {
            margin-top: 0.9rem;
            border: none;
            border-radius: 10px;
            background: var(--brand-maroon);
            color: #ffffff;
            font-size: 0.86rem;
            font-weight: 700;
            padding: 0.58rem 1rem;
            cursor: pointer;
        }
        .checkout-popup-close:hover { background: var(--brand-maroon-dark); }

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
            text-transform: uppercase; color: rgba(255,205,210,.92); margin-bottom: 4px;
        }

        .banner-title {
            font-size: 1.25rem; font-weight: 700; color: #fff;
            line-height: 1.2; margin-bottom: 10px;
        }

        .banner-meta { display: flex; flex-direction: column; gap: 4px; }

        .banner-meta-item {
            font-size: 11px; color: rgba(247,244,245,.78);
            display: flex; align-items: center; gap: 5px;
        }

        .banner-meta-item svg { opacity: 0.6; flex-shrink: 0; }

        .order-summary {
            background: var(--bg-panel); border: 1px solid var(--border);
            border-radius: 16px; padding: 18px 20px;
        }

        .os-title {
            font-size: 10px; font-weight: 700; letter-spacing: 0.12em;
            text-transform: uppercase; color: rgba(255,205,210,.88); margin-bottom: 10px;
        }

        .os-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 9px 0; border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .os-row:last-of-type { border-bottom: none; }
        .os-label { font-size: 12px; color: rgba(247,244,245,.72); }
        .os-val { font-size: 13px; font-weight: 600; color: #fff; }

        .os-total {
            display: flex; justify-content: space-between; align-items: center;
            margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,.1);
        }

        .os-total-label { font-size: 13px; font-weight: 700; color: #fff; }
        .os-total-val { font-size: 16px; font-weight: 700; color: var(--brand-blue); }
        .os-note {
            margin: 10px 0 0;
            font-size: 11px;
            line-height: 1.45;
            color: rgba(247,244,245,.72);
        }

        .checkout-back {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; color: rgba(247,244,245,.56); text-decoration: none;
            transition: color 0.15s;
        }

        .checkout-back:hover { color: rgba(255,205,210,.92); }

        /* ── Right ── */
        .checkout-right {
            background: var(--bg-panel); border: 1px solid var(--border);
            border-radius: 16px; padding: 22px;
            display: flex; flex-direction: column; gap: 16px;
        }

        .form-title { font-size: 15px; font-weight: 700; color: #fff; }
        .form-sub { font-size: 12px; color: rgba(247,244,245,.72); margin-top: -8px; }

        .field { display: flex; flex-direction: column; gap: 7px; }

        .field span, .field-span {
            font-size: 11px; font-weight: 600; color: rgba(247,244,245,.9); letter-spacing: 0.04em;
        }

        .field select,
        .field input[type="text"],
        .field input[type="number"] {
            width: 100%; height: 42px;
            border: 1px solid rgba(255,255,255,.18); border-radius: 10px;
            background: var(--bg-input); color: #fff;
            padding: 0 12px; font-size: 13px;
            transition: border-color 0.15s, box-shadow 0.15s;
            appearance: auto;
        }

        .field select:focus,
        .field input:focus {
            outline: none;
            border-color: rgba(192,40,60,.65);
            box-shadow: 0 0 0 3px rgba(192,40,60,.14);
        }

        .field select option { background: var(--bg-input); color: #fff; }
        .field small, .field-error { font-size: 11px; color: #ff99a2; font-weight: 600; }

        .field-divider { height: 1px; background: rgba(255,255,255,.09); }

        .guest-warning {
            border: 1px solid rgba(192,40,60,.3);
            background: rgba(192,40,60,.1);
            border-radius: 12px;
            padding: 0.7rem 0.8rem;
            display: grid;
            gap: 0.25rem;
        }

        .guest-warning-title {
            margin: 0;
            font-size: 0.8rem;
            font-weight: 700;
            color: #ffe3e6;
        }

        .guest-warning-text {
            margin: 0;
            font-size: 0.72rem;
            line-height: 1.45;
            color: rgba(247,244,245,.76);
        }

        .account-opt-in {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.76rem;
            color: var(--text-main);
            font-weight: 600;
            cursor: pointer;
        }

        .account-opt-in input {
            width: 15px;
            height: 15px;
            accent-color: var(--brand-maroon);
        }

        .password-block {
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 10px;
            padding: 0.7rem;
            display: grid;
            gap: 0.65rem;
            background: var(--bg-input);
        }

        .provider-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 4px; }

        .provider-btn {
            border: 1px solid rgba(255,255,255,.18); border-radius: 10px;
            background: var(--bg-input); padding: 10px 12px;
            display: flex; align-items: center; gap: 8px;
            cursor: pointer; transition: border-color 0.15s, background 0.15s;
        }

        .provider-btn:hover { border-color: rgba(192,40,60,.55); }

        .provider-btn.is-selected {
            border-color: var(--brand-maroon);
            background: rgba(192,40,60,.12);
        }

        .provider-icon {
            width: 36px; height: 36px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; overflow: hidden;
        }

        .provider-icon img { width: 100%; height: 100%; object-fit: cover; display: block; }

        .mtn-icon { background: #ffcc00; }
        .airtel-icon { background: #fff; }

        .provider-name { font-size: 12px; font-weight: 600; color: rgba(247,244,245,.86); }
        .provider-btn.is-selected .provider-name { color: #fff; }

        .checkout-btn {
            height: 50px; border: none; border-radius: 12px;
            background: #1255c0; color: #fff;
            font-size: 14px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.15s;
        }

        .checkout-btn:hover { background: #0e3fa0; }

        .checkout-charge-line {
            margin: 0 0 4px;
            font-size: 12px;
            font-weight: 700;
            color: rgba(255,232,236,.95);
            text-align: center;
        }

        .secure-note {
            text-align: center; font-size: 11px; color: rgba(247,244,245,.74);
            display: flex; align-items: center; justify-content: center; gap: 4px;
        }

        .promo-section { margin-bottom: 12px; }
        .promo-row { display: flex; gap: 8px; }
        .promo-input {
            flex: 1; padding: 10px 12px; border: 1px solid rgba(255,255,255,.18); border-radius: 8px;
            font-size: 13px; background: var(--bg-input); color: #fff; outline: none;
            text-transform: uppercase;
        }
        .promo-input:focus { border-color: rgba(192,40,60,.65); }
        .promo-btn {
            padding: 10px 16px; background: var(--brand-maroon); color: #fff; border: none;
            border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;
            white-space: nowrap;
        }
        .promo-btn:hover:not(:disabled) { background: var(--brand-maroon-dark); }
        .promo-btn:disabled { opacity: 0.6; cursor: default; }
        .promo-feedback { font-size: 12px; margin-top: 5px; font-weight: 600; }

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
            const summaryDiscountRow = document.getElementById('summary-discount-row');
            const summaryDiscount = document.getElementById('summary-discount');
            const summaryServerNote = document.getElementById('summary-server-note');
            const chargeLine = document.getElementById('checkout-charge-line');
            const providerInput = document.getElementById('payment_provider_input');
            const providerBtns = document.querySelectorAll('.provider-btn');
            const popup = document.getElementById('checkout-popup');
            const popupClose = document.getElementById('checkout-popup-close');
            const createAccountCheckbox = document.getElementById('create_account');
            const passwordFields = document.getElementById('account_password_fields');
            const promoCodeInput = document.getElementById('promo_code_input');
            const promoField = document.getElementById('promo_code_field');
            const promoApplyBtn = document.getElementById('promo-apply-btn');
            const promoFeedback = document.getElementById('promo-feedback');

            const formatUgx = (n) => n <= 0 ? 'Free' : 'UGX ' + Math.round(n).toLocaleString('en-US');
            const paymentFields = document.getElementById('payment-fields');
            const secureNote = document.getElementById('secure-note');
            const submitBtn = document.getElementById('checkout-submit-btn');

            let appliedDiscount = 0; // discount amount in UGX, set by promo AJAX

            const updateSummary = () => {
                const selected = categorySelect?.options[categorySelect.selectedIndex];
                const price = parseFloat(selected?.dataset.price || 0);
                const label = selected?.dataset.label || '—';
                const priceLabel = selected?.dataset.priceLabel || '—';
                const qty = parseInt(quantityInput?.value || 1, 10);
                const subtotal = price * qty;
                const finalTotal = Math.max(0, subtotal - appliedDiscount);
                const isFree = finalTotal <= 0;

                if (summaryCategory) summaryCategory.textContent = label;
                if (summaryPrice) summaryPrice.textContent = priceLabel;
                if (summaryQty) summaryQty.textContent = qty;

                if (appliedDiscount > 0 && summaryDiscountRow && summaryDiscount) {
                    summaryDiscountRow.style.display = '';
                    summaryDiscount.textContent = '− ' + formatUgx(appliedDiscount);
                } else if (summaryDiscountRow) {
                    summaryDiscountRow.style.display = 'none';
                }

                if (summaryTotal) summaryTotal.textContent = isFree ? 'Free' : formatUgx(finalTotal);
                if (chargeLine) {
                    chargeLine.textContent = isFree
                        ? 'You will be charged: Free'
                        : `You will be charged: ${formatUgx(finalTotal)}`;
                }
                if (summaryServerNote) {
                    summaryServerNote.textContent = isFree
                        ? 'Free ticket. Availability is confirmed on the server before issuing your ticket.'
                        : 'Amount is verified on the server before payment is initiated.';
                }

                if (paymentFields) paymentFields.style.display = isFree ? 'none' : '';
                if (secureNote) secureNote.style.display = isFree ? 'none' : '';
                if (submitBtn) submitBtn.textContent = isFree ? 'Get Ticket →' : 'Initiate payment →';
            };

            // Promo code AJAX
            const applyPromo = async () => {
                const code = promoField?.value.trim();
                if (!code) return;

                const selected = categorySelect?.options[categorySelect.selectedIndex];
                const price = parseFloat(selected?.dataset.price || 0);
                const qty = parseInt(quantityInput?.value || 1, 10);
                const subtotal = price * qty;

                promoApplyBtn.disabled = true;
                promoApplyBtn.textContent = '…';

                try {
                    const res = await fetch('{{ route('promo.validate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            code,
                            event_id: {{ $event->id }},
                            subtotal,
                        }),
                    });

                    const json = await res.json();

                    if (json.ok) {
                        appliedDiscount = json.discount_amount;
                        if (promoCodeInput) promoCodeInput.value = code.toUpperCase();
                        showFeedback('✓ ' + json.message, true);
                        promoApplyBtn.textContent = 'Applied';
                        promoApplyBtn.disabled = true;
                    } else {
                        appliedDiscount = 0;
                        if (promoCodeInput) promoCodeInput.value = '';
                        showFeedback(json.message || 'Invalid promo code.', false);
                        promoApplyBtn.textContent = 'Apply';
                        promoApplyBtn.disabled = false;
                    }
                } catch {
                    promoApplyBtn.textContent = 'Apply';
                    promoApplyBtn.disabled = false;
                }

                updateSummary();
            };

            const showFeedback = (msg, ok) => {
                if (!promoFeedback) return;
                promoFeedback.textContent = msg;
                promoFeedback.style.display = '';
                promoFeedback.style.color = ok ? '#7ad89a' : '#ff99a2';
            };

            if (promoApplyBtn) promoApplyBtn.addEventListener('click', applyPromo);
            if (promoField) promoField.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); applyPromo(); } });

            // Reset discount when category or qty changes (code may no longer apply)
            const resetPromo = () => {
                appliedDiscount = 0;
                if (promoCodeInput) promoCodeInput.value = '';
                if (promoField) promoField.value = '';
                if (promoFeedback) promoFeedback.style.display = 'none';
                if (promoApplyBtn) { promoApplyBtn.textContent = 'Apply'; promoApplyBtn.disabled = false; }
            };

            if (categorySelect) categorySelect.addEventListener('change', () => { resetPromo(); updateSummary(); });
            if (quantityInput) quantityInput.addEventListener('input', () => { resetPromo(); updateSummary(); });

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