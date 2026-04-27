@extends('layouts.app')

@section('content')

<div class="tp-page">

    {{-- ── Hero ── --}}
    <section class="tp-hero">
        <div class="tp-hero-inner">
            <p class="tp-eyebrow">FOR EVENT ORGANISERS</p>
            <h1 class="tp-heading">Ticket Packages &amp; Services</h1>
            <p class="tp-sub">Everything you need to run a smooth, professional event — from online sales to gate management. Choose the package that fits your event.</p>
        </div>
    </section>

    {{-- ── Packages grid ── --}}
    <section class="tp-packages">
        <div class="tp-shell">
            @foreach ($packages as $i => $pkg)
                <article class="tp-card {{ $i % 2 === 1 ? 'tp-card--flip' : '' }}">
                    <div class="tp-card-media">
                        @if (!empty($pkg['image']))
                            <img src="{{ $pkg['image'] }}" alt="{{ $pkg['label'] }}">
                        @else
                            <div class="tp-card-media-placeholder"></div>
                        @endif
                    </div>
                    <div class="tp-card-body">
                        <span class="tp-badge">{{ $pkg['label'] }}</span>
                        <h2 class="tp-card-title">{{ $pkg['title'] }}</h2>
                        <p class="tp-card-copy">{{ $pkg['copy'] }}</p>
                        <button type="button" class="tp-card-cta"
                                data-package="{{ $pkg['label'] }}"
                                onclick="openEnquiry(this.dataset.package)">Get in touch</button>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- ── Bottom CTA ── --}}
    <section class="tp-cta">
        <div class="tp-shell">
            <div class="tp-cta-box">
                <h2>Not sure which package suits your event?</h2>
                <p>Reach out and we'll walk you through the best option for your audience size, venue type, and budget.</p>
                <button type="button" class="tp-cta-btn" onclick="openEnquiry()">Contact us</button>
            </div>
        </div>
    </section>

</div>

<style>
    .tp-page {
        padding-top: 5.5rem;
        background: #1a0a0e;
        min-height: 100vh;
    }

    .tp-shell {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 1.25rem;
    }

    /* ── Hero ── */
    .tp-hero {
        padding: 4rem 1.25rem 3.5rem;
        text-align: center;
        background: linear-gradient(160deg, #2c0d16 0%, #1a0a0e 60%);
        border-bottom: 1px solid rgba(255,255,255,.07);
    }

    .tp-hero-inner {
        max-width: 680px;
        margin: 0 auto;
    }

    .tp-eyebrow {
        margin: 0 0 0.85rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        color: #e8a06a;
        text-transform: uppercase;
    }

    .tp-heading {
        margin: 0 0 1rem;
        font-size: clamp(1.9rem, 4vw, 2.8rem);
        font-weight: 800;
        line-height: 1.1;
        color: #fff;
    }

    .tp-sub {
        margin: 0;
        font-size: 1.02rem;
        line-height: 1.65;
        color: rgba(255,255,255,.72);
    }

    /* ── Packages ── */
    .tp-packages {
        padding: 4rem 0 3rem;
    }

    .tp-shell > .tp-card + .tp-card {
        margin-top: 2.5rem;
    }

    .tp-card {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.09);
        background: rgba(255,255,255,.03);
        transition: border-color .2s;
    }

    .tp-card:hover {
        border-color: rgba(232,160,106,.35);
    }

    .tp-card--flip .tp-card-media { order: 2; }
    .tp-card--flip .tp-card-body  { order: 1; }

    .tp-card-media {
        position: relative;
        min-height: 300px;
        overflow: hidden;
    }

    .tp-card-media img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .5s ease;
    }

    .tp-card:hover .tp-card-media img {
        transform: scale(1.04);
    }

    .tp-card-media-placeholder {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, #2c1020 0%, #3a1530 100%);
    }

    .tp-card-body {
        padding: 2.5rem 2rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 1rem;
    }

    .tp-badge {
        display: inline-flex;
        align-self: flex-start;
        border-radius: 999px;
        background: rgba(232,160,106,.15);
        border: 1px solid rgba(232,160,106,.4);
        color: #e8a06a;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        padding: 0.3rem 0.75rem;
    }

    .tp-card-title {
        margin: 0;
        font-size: clamp(1.1rem, 3.5vw, 1.45rem);
        font-weight: 800;
        line-height: 1.25;
        color: #fff;
    }

    .tp-card-copy {
        margin: 0;
        font-size: 0.93rem;
        line-height: 1.7;
        color: rgba(255,255,255,.68);
    }

    .tp-card-cta {
        display: inline-flex;
        align-self: flex-start;
        padding: 0.6rem 1.4rem;
        border-radius: 999px;
        background: #e8a06a;
        color: #1a0a0e;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        transition: background .15s, transform .15s;
        margin-top: 0.25rem;
    }

    .tp-card-cta:hover {
        background: #f5b87a;
        transform: translateY(-1px);
    }

    /* ── Bottom CTA ── */
    .tp-cta {
        padding: 3rem 0 5rem;
    }

    .tp-cta-box {
        background: linear-gradient(130deg, #2c0d16 0%, #1f1030 100%);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 20px;
        padding: 3rem 2.5rem;
        text-align: center;
        display: grid;
        gap: 0.9rem;
    }

    .tp-cta-box h2 {
        margin: 0;
        font-size: clamp(1.3rem, 2.5vw, 1.8rem);
        font-weight: 800;
        color: #fff;
    }

    .tp-cta-box p {
        margin: 0;
        color: rgba(255,255,255,.65);
        font-size: 0.97rem;
        line-height: 1.6;
        max-width: 54ch;
        margin-inline: auto;
    }

    .tp-cta-btn {
        display: inline-flex;
        align-self: center;
        justify-self: center;
        padding: 0.75rem 2rem;
        border-radius: 999px;
        background: #fff;
        color: #1a0a0e;
        font-size: 0.95rem;
        font-weight: 700;
        text-decoration: none;
        margin-top: 0.5rem;
        transition: background .15s, transform .15s;
    }

    .tp-cta-btn:hover {
        background: #f0f0f0;
        transform: translateY(-1px);
    }

    /* ── Responsive ── */

    /* Tablet landscape */
    @media (max-width: 900px) {
        .tp-card-body {
            padding: 2rem 1.75rem;
        }
    }

    /* Tablet portrait — stack cards */
    @media (max-width: 720px) {
        .tp-page {
            padding-top: 4.5rem;
        }

        .tp-hero {
            padding: 3rem 1.25rem 2.5rem;
        }

        .tp-card {
            grid-template-columns: 1fr;
            border-radius: 16px;
        }

        /* restore DOM order — no flip on mobile */
        .tp-card--flip .tp-card-media { order: unset; }
        .tp-card--flip .tp-card-body  { order: unset; }

        .tp-card-media {
            min-height: 220px;
        }

        .tp-card-body {
            padding: 1.75rem 1.5rem;
            gap: 0.85rem;
        }

        .tp-packages {
            padding: 2.5rem 0 2rem;
        }

        .tp-shell > .tp-card + .tp-card {
            margin-top: 1.25rem;
        }

        .tp-cta-box {
            padding: 2rem 1.5rem;
            border-radius: 16px;
        }

        .tp-cta {
            padding: 2rem 0 3.5rem;
        }
    }

    /* Mobile */
    @media (max-width: 480px) {
        .tp-page {
            padding-top: 4rem;
        }

        .tp-hero {
            padding: 2.5rem 1rem 2rem;
        }

        .tp-heading {
            font-size: 1.65rem;
            line-height: 1.15;
        }

        .tp-sub {
            font-size: 0.92rem;
        }

        .tp-card {
            border-radius: 14px;
        }

        .tp-card-media {
            min-height: 190px;
        }

        .tp-card-body {
            padding: 1.35rem 1.1rem;
            gap: 0.75rem;
        }

        .tp-card-title {
            font-size: 1.1rem;
        }

        .tp-card-copy {
            font-size: 0.88rem;
        }

        .tp-card-cta {
            width: 100%;
            justify-content: center;
            padding: 0.65rem 1rem;
        }

        .tp-shell {
            padding: 0 0.85rem;
        }

        .tp-shell > .tp-card + .tp-card {
            margin-top: 1rem;
        }

        .tp-cta-box {
            padding: 1.75rem 1.1rem;
            border-radius: 14px;
        }

        .tp-cta-box h2 {
            font-size: 1.2rem;
        }

        .tp-cta-box p {
            font-size: 0.88rem;
        }

        .tp-cta-btn {
            width: 100%;
            justify-content: center;
        }
    }

    /* Small phones */
    @media (max-width: 360px) {
        .tp-heading {
            font-size: 1.45rem;
        }

        .tp-card-media {
            min-height: 160px;
        }
    }
</style>

{{-- ── Enquiry Modal ── --}}
<div class="enq-overlay" id="enq-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="enq-title">
    <div class="enq-backdrop" id="enq-backdrop"></div>

    <div class="enq-dialog">
        <button class="enq-close" id="enq-close" aria-label="Close enquiry form">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>

        {{-- Form state --}}
        <div id="enq-form-wrap">
            <div class="enq-header">
                <p class="enq-eyebrow">GET IN TOUCH</p>
                <h2 class="enq-title" id="enq-title">Tell us about your event</h2>
                <p class="enq-sub">Fill in the details and we'll get back to you within 24 hours.</p>
            </div>

            <form id="enq-form" novalidate>
                @csrf
                <div class="enq-grid">
                    <div class="enq-field">
                        <label for="enq-name">Full Name <span class="enq-req">*</span></label>
                        <input type="text" id="enq-name" name="name" required autocomplete="name" placeholder="Jane Doe">
                        <span class="enq-field-err" id="err-name"></span>
                    </div>

                    <div class="enq-field">
                        <label for="enq-email">Email Address <span class="enq-req">*</span></label>
                        <input type="email" id="enq-email" name="email" required autocomplete="email" placeholder="jane@example.com">
                        <span class="enq-field-err" id="err-email"></span>
                    </div>

                    <div class="enq-field">
                        <label for="enq-phone">Phone Number</label>
                        <input type="tel" id="enq-phone" name="phone" autocomplete="tel" placeholder="+256 700 000 000">
                    </div>

                    <div class="enq-field">
                        <label for="enq-package">Package <span class="enq-req">*</span></label>
                        <select id="enq-package" name="package" required>
                            @foreach ($packages as $pkg)
                                <option value="{{ $pkg['label'] }}">{{ $pkg['label'] }}</option>
                            @endforeach
                        </select>
                        <span class="enq-field-err" id="err-package"></span>
                    </div>

                    <div class="enq-field">
                        <label for="enq-date">Event Date</label>
                        <input type="date" id="enq-date" name="event_date">
                    </div>

                    <div class="enq-field">
                        <label for="enq-attendance">Expected Attendance</label>
                        <input type="text" id="enq-attendance" name="attendance" placeholder="e.g. 200–300 guests">
                    </div>

                    <div class="enq-field enq-field--full">
                        <label for="enq-message">Additional Details</label>
                        <textarea id="enq-message" name="message" rows="4"
                            placeholder="Tell us about your event, venue, or any specific requirements…"></textarea>
                    </div>
                </div>

                <div class="enq-form-error" id="enq-form-error" hidden>
                    Something went wrong. Please try again or email us directly at <a href="mailto:wadoconcepts@gmail.com">wadoconcepts@gmail.com</a>.
                </div>

                <button type="submit" class="enq-submit" id="enq-submit">
                    <span id="enq-submit-text">Send Enquiry</span>
                    <span id="enq-submit-spinner" hidden>
                        <svg class="enq-spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9" stroke-dasharray="28.3" stroke-dashoffset="28.3" stroke-linecap="round"/></svg>
                        Sending…
                    </span>
                </button>
            </form>
        </div>

        {{-- Success state --}}
        <div id="enq-success" hidden>
            <div class="enq-success-wrap">
                <div class="enq-success-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-5"/></svg>
                </div>
                <h2 class="enq-success-title">Enquiry Sent!</h2>
                <p class="enq-success-msg">Thanks! We'll review your details and get back to you within 24 hours.</p>
                <button type="button" class="enq-submit" id="enq-done">Done</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* ── Modal overlay ── */
    .enq-overlay {
        position: fixed;
        inset: 0;
        z-index: 200;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity .28s ease, visibility .28s ease;
    }
    .enq-overlay.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
    .enq-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(4, 6, 14, .80);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    /* ── Dialog box ── */
    .enq-dialog {
        position: relative;
        z-index: 1;
        width: min(760px, 100%);
        max-height: 92vh;
        overflow-y: auto;
        border-radius: 20px;
        background: #16080d;
        border: 1px solid rgba(255,255,255,.1);
        box-shadow: 0 30px 80px rgba(0,0,0,.6), 0 0 0 1px rgba(192,40,60,.15);
        padding: 2.5rem;
        transform: translateY(20px) scale(.97);
        transition: transform .3s ease, opacity .28s ease;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,.1) transparent;
    }
    .enq-overlay.is-open .enq-dialog {
        transform: translateY(0) scale(1);
    }

    /* ── Close button ── */
    .enq-close {
        position: absolute;
        top: 1.1rem;
        right: 1.1rem;
        width: 2.1rem;
        height: 2.1rem;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,.14);
        background: rgba(255,255,255,.07);
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s;
        flex-shrink: 0;
    }
    .enq-close:hover { background: rgba(192,40,60,.35); }
    .enq-close svg { width: 1rem; height: 1rem; }

    /* ── Header ── */
    .enq-header { margin-bottom: 1.75rem; padding-right: 2rem; }
    .enq-eyebrow {
        margin: 0 0 .6rem;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .18em;
        color: rgba(255,160,170,.85);
        text-transform: uppercase;
    }
    .enq-title {
        margin: 0 0 .5rem;
        font-size: clamp(1.25rem, 3vw, 1.7rem);
        font-weight: 800;
        color: #fff;
        line-height: 1.15;
    }
    .enq-sub {
        margin: 0;
        font-size: .9rem;
        color: rgba(255,255,255,.55);
        line-height: 1.55;
    }

    /* ── Form grid ── */
    .enq-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem 1.25rem;
        margin-bottom: 1.25rem;
    }
    .enq-field { display: flex; flex-direction: column; gap: .38rem; }
    .enq-field--full { grid-column: 1 / -1; }

    .enq-field label {
        font-size: .78rem;
        font-weight: 700;
        color: rgba(255,255,255,.7);
        letter-spacing: .03em;
    }
    .enq-req { color: #e8a06a; }

    .enq-field input,
    .enq-field select,
    .enq-field textarea {
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.14);
        border-radius: 10px;
        padding: .65rem .9rem;
        color: #fff;
        font-size: .92rem;
        font-family: inherit;
        outline: none;
        transition: border-color .18s, box-shadow .18s;
        width: 100%;
        box-sizing: border-box;
    }
    .enq-field input::placeholder,
    .enq-field textarea::placeholder { color: rgba(255,255,255,.28); }
    .enq-field input:focus,
    .enq-field select:focus,
    .enq-field textarea:focus {
        border-color: #c0283c;
        box-shadow: 0 0 0 3px rgba(192,40,60,.2);
    }
    .enq-field input.is-invalid,
    .enq-field select.is-invalid { border-color: #f87171; box-shadow: 0 0 0 3px rgba(248,113,113,.18); }
    .enq-field select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='rgba(255,255,255,0.45)' stroke-width='2' stroke-linecap='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .75rem center;
        background-size: 1rem;
        padding-right: 2.4rem;
        cursor: pointer;
    }
    .enq-field select option { background: #1a0a0e; color: #fff; }
    .enq-field textarea { resize: vertical; min-height: 100px; }

    .enq-field-err {
        font-size: .74rem;
        color: #f87171;
        min-height: 1em;
    }

    /* ── Form-level error ── */
    .enq-form-error {
        background: rgba(248,113,113,.1);
        border: 1px solid rgba(248,113,113,.3);
        border-radius: 10px;
        padding: .75rem 1rem;
        font-size: .85rem;
        color: #fca5a5;
        margin-bottom: 1.1rem;
        line-height: 1.5;
    }
    .enq-form-error a { color: #fca5a5; }

    /* ── Submit button ── */
    .enq-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        width: 100%;
        padding: .8rem 1.5rem;
        border-radius: 999px;
        border: none;
        background: #c0283c;
        color: #fff;
        font-size: .95rem;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(192,40,60,.35);
        transition: background .15s, transform .15s, box-shadow .15s;
    }
    .enq-submit:hover:not(:disabled) { background: #a01e2e; transform: translateY(-1px); box-shadow: 0 6px 22px rgba(192,40,60,.45); }
    .enq-submit:disabled { opacity: .6; cursor: not-allowed; }

    /* ── Spinner ── */
    @keyframes enqSpin { to { transform: rotate(360deg); } }
    .enq-spinner {
        width: 1rem; height: 1rem;
        animation: enqSpin .7s linear infinite;
    }

    /* ── Success state ── */
    .enq-success-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 1.5rem 0 .5rem;
        gap: 1rem;
    }
    .enq-success-icon {
        width: 4rem; height: 4rem;
        border-radius: 999px;
        background: rgba(34,197,94,.12);
        border: 1.5px solid rgba(34,197,94,.35);
        display: flex; align-items: center; justify-content: center;
    }
    .enq-success-icon svg { width: 2rem; height: 2rem; stroke: #22c55e; }
    .enq-success-title { margin: 0; font-size: 1.6rem; font-weight: 800; color: #fff; }
    .enq-success-msg { margin: 0; font-size: .95rem; color: rgba(255,255,255,.6); line-height: 1.6; max-width: 38ch; }

    /* ── Responsive ── */
    @media (max-width: 600px) {
        .enq-dialog { padding: 1.75rem 1.25rem; border-radius: 16px; }
        .enq-grid { grid-template-columns: 1fr; }
        .enq-field--full { grid-column: 1; }
        .enq-header { margin-bottom: 1.25rem; }
        .enq-overlay { padding: 0; align-items: flex-end; }
        .enq-dialog { border-radius: 20px 20px 0 0; max-height: 95vh; width: 100%; }
    }
    @media (max-width: 400px) {
        .enq-dialog { padding: 1.5rem 1rem; }
    }
</style>

<script>
(() => {
    const overlay   = document.getElementById('enq-overlay');
    const backdrop  = document.getElementById('enq-backdrop');
    const closeBtn  = document.getElementById('enq-close');
    const form      = document.getElementById('enq-form');
    const formWrap  = document.getElementById('enq-form-wrap');
    const success   = document.getElementById('enq-success');
    const submitBtn = document.getElementById('enq-submit');
    const submitTxt = document.getElementById('enq-submit-text');
    const submitSpin = document.getElementById('enq-submit-spinner');
    const formErr   = document.getElementById('enq-form-error');
    const pkgSelect = document.getElementById('enq-package');

    window.openEnquiry = function (packageLabel) {
        if (packageLabel && pkgSelect) {
            for (const opt of pkgSelect.options) {
                opt.selected = opt.value === packageLabel;
            }
        }
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            const first = form.querySelector('input, select, textarea');
            if (first) first.focus();
        }, 60);
    };

    function closeEnquiry() {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function resetModal() {
        form.reset();
        formWrap.hidden = false;
        success.hidden  = true;
        formErr.hidden  = true;
        clearFieldErrors();
        setSubmitting(false);
    }

    function setSubmitting(on) {
        submitBtn.disabled    = on;
        submitTxt.hidden      = on;
        submitSpin.hidden     = !on;
    }

    function clearFieldErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.enq-field-err').forEach(el => el.textContent = '');
    }

    function showFieldError(name, msg) {
        const input = form.querySelector(`[name="${name}"]`);
        const errEl = document.getElementById(`err-${name}`);
        if (input)  input.classList.add('is-invalid');
        if (errEl)  errEl.textContent = msg;
    }

    closeBtn.addEventListener('click', closeEnquiry);
    backdrop.addEventListener('click', closeEnquiry);
    document.getElementById('enq-done').addEventListener('click', () => { closeEnquiry(); setTimeout(resetModal, 350); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && overlay.classList.contains('is-open')) closeEnquiry(); });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearFieldErrors();
        formErr.hidden = true;

        const data = new FormData(form);

        // Basic client-side required check
        let hasError = false;
        if (!data.get('name').trim())    { showFieldError('name', 'Please enter your name.'); hasError = true; }
        if (!data.get('email').trim())   { showFieldError('email', 'Please enter your email address.'); hasError = true; }
        if (!data.get('package').trim()) { showFieldError('package', 'Please select a package.'); hasError = true; }
        if (hasError) return;

        setSubmitting(true);

        try {
            const res = await fetch('{{ route('ticket-packages.enquire') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': data.get('_token'), 'Accept': 'application/json' },
                body: data,
            });

            const json = await res.json();

            if (res.status === 422 && json.errors) {
                Object.entries(json.errors).forEach(([field, msgs]) => {
                    showFieldError(field, msgs[0]);
                });
                setSubmitting(false);
                return;
            }

            if (!res.ok) throw new Error('Server error');

            formWrap.hidden = true;
            success.hidden  = false;

        } catch {
            formErr.hidden = false;
            setSubmitting(false);
        }
    });
})();
</script>

@endsection
