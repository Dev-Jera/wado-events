@extends('layouts.app')

@section('content')

<div class="ct-page">

    {{-- ── Hero ── --}}
    <section class="ct-hero">
        <div class="ct-hero-inner">
            <p class="ct-eyebrow">GET IN TOUCH</p>
            <h1 class="ct-heading">We'd love to hear from you</h1>
            <p class="ct-sub">Whether you have a question about an event, need support with a ticket, or just want to say hello, we are here.</p>
        </div>
    </section>

    {{-- ── Main content ── --}}
    <section class="ct-body">
        <div class="ct-shell">
            <div class="ct-layout">

                {{-- ── Left: info column ── --}}
                <div class="ct-info">

                    <div class="ct-info-block">
                        <h2 class="ct-info-title">About WADO</h2>
                        <p class="ct-info-text">WADO is an event ticketing company based in Kampala, Uganda. We help event organisers sell tickets online, produce printed tickets and wristbands, and manage gate operations, all from one platform. We work with artists, promoters, venue managers and community organisers across Uganda to make events run smoothly.</p>
                    </div>

                    <div class="ct-info-block">
                        <h2 class="ct-info-title">Find us</h2>
                        <div class="ct-details">
                            <div class="ct-detail-row">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                                <span>Kireka, opposite Shell<br>Lico Holdings Limited building<br>Kampala, Uganda</span>
                            </div>
                            <div class="ct-detail-row">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                <a href="mailto:wadoconcepts@gmail.com">wadoconcepts@gmail.com</a>
                            </div>
                            <div class="ct-detail-row">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.85 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.77 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.74a16 16 0 0 0 6.29 6.29l1.1-1.1a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                <a href="tel:+256703015846">0703 015 846</a>
                            </div>
                            <div class="ct-detail-row">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>Mon – Fri, 9am – 6pm EAT</span>
                            </div>
                        </div>
                    </div>

                    <div class="ct-info-block">
                        <h2 class="ct-info-title">Response time</h2>
                        <p class="ct-info-text">We respond to all enquiries within 24 hours on business days. For urgent event-day issues, mention it in your message and we will prioritise accordingly.</p>
                    </div>

                </div>

                {{-- ── Right: form ── --}}
                <div class="ct-form-wrap">
                    <div class="ct-form-card">
                        <h2 class="ct-form-title">Send us a message</h2>

                        <form id="ct-form" novalidate>
                            @csrf
                            <div class="ct-fields">

                                <div class="ct-field ct-field--half">
                                    <label for="ct-name">Full name <span class="ct-req">*</span></label>
                                    <input type="text" id="ct-name" name="name" required autocomplete="name" placeholder="Jane Doe">
                                    <span class="ct-field-err" id="ct-err-name"></span>
                                </div>

                                <div class="ct-field ct-field--half">
                                    <label for="ct-email">Email address <span class="ct-req">*</span></label>
                                    <input type="email" id="ct-email" name="email" required autocomplete="email" placeholder="jane@example.com">
                                    <span class="ct-field-err" id="ct-err-email"></span>
                                </div>

                                <div class="ct-field ct-field--half">
                                    <label for="ct-phone">Phone number</label>
                                    <input type="tel" id="ct-phone" name="phone" autocomplete="tel" placeholder="+256 700 000 000">
                                </div>

                                <div class="ct-field ct-field--half">
                                    <label for="ct-type">What is this about? <span class="ct-req">*</span></label>
                                    <select id="ct-type" name="package" required>
                                        <option value="">Select a topic</option>
                                        <option value="General question">General question</option>
                                        <option value="Ticket or event support">Ticket or event support</option>
                                        <option value="Ticket packages enquiry">Ticket packages enquiry</option>
                                        <option value="Partnership or collaboration">Partnership or collaboration</option>
                                        <option value="Report an issue">Report an issue</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <span class="ct-field-err" id="ct-err-package"></span>
                                </div>

                                <div class="ct-field ct-field--full">
                                    <label for="ct-message">Your message <span class="ct-req">*</span></label>
                                    <textarea id="ct-message" name="message" rows="5" required
                                        placeholder="Tell us what's on your mind. The more detail you share, the better we can help."></textarea>
                                    <span class="ct-field-err" id="ct-err-message"></span>
                                </div>

                            </div>

                            <div class="ct-form-error" id="ct-form-error" hidden>
                                Something went wrong. Please try again or email us at <a href="mailto:wadoconcepts@gmail.com">wadoconcepts@gmail.com</a>.
                            </div>

                            <button type="submit" class="ct-submit" id="ct-submit">
                                <span id="ct-submit-text">Send message</span>
                                <span id="ct-submit-spinner" hidden>
                                    <svg class="ct-spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9" stroke-dasharray="28.3" stroke-dashoffset="28.3" stroke-linecap="round"/></svg>
                                    Sending…
                                </span>
                            </button>
                        </form>

                        <div id="ct-success" hidden>
                            <div class="ct-success-wrap">
                                <div class="ct-success-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m8 12 3 3 5-5"/></svg>
                                </div>
                                <h3>Message received</h3>
                                <p>Thanks for reaching out. We will get back to you within 24 hours.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ── FAQ ── --}}
    <section class="ct-faq">
        <div class="ct-shell">
            <p class="ct-eyebrow" style="text-align:center;margin-bottom:.6rem">COMMON QUESTIONS</p>
            <h2 class="ct-faq-title">Frequently asked questions</h2>

            <div class="ct-faq-grid">

                <div class="ct-faq-item">
                    <h3>How do I receive my ticket after purchase?</h3>
                    <p>Your ticket is sent to the email address you used at checkout. Check your spam folder if it doesn't arrive within a few minutes. The ticket contains a unique QR code that is scanned at the gate.</p>
                </div>

                <div class="ct-faq-item">
                    <h3>Can I get a refund on my ticket?</h3>
                    <p>Refund eligibility depends on the event organiser's policy. If the event is cancelled by the organiser, you will receive a full refund. For other cases, contact us with your ticket reference and we will look into it.</p>
                </div>

                <div class="ct-faq-item">
                    <h3>What payment methods do you accept?</h3>
                    <p>We accept MTN Mobile Money, Airtel Money, and major debit and credit cards. All payments are processed securely and you receive confirmation immediately after a successful transaction.</p>
                </div>

                <div class="ct-faq-item">
                    <h3>How do I list my event on WADO?</h3>
                    <p>Get in touch using the form on this page and select "Ticket packages enquiry" as the topic. We will walk you through the options and get your event set up on the platform.</p>
                </div>

                <div class="ct-faq-item">
                    <h3>My QR code is not scanning at the gate. What do I do?</h3>
                    <p>Show the gate staff the full email on your phone, including the ticket details and QR code. If the issue persists, ask them to contact WADO support directly so we can verify your ticket in real time.</p>
                </div>

                <div class="ct-faq-item">
                    <h3>Can I transfer my ticket to someone else?</h3>
                    <p>Tickets are tied to the buyer's email but the QR code itself can be used by whoever presents it at the gate. If you need to formally transfer ownership, contact us before the event day.</p>
                </div>

                <div class="ct-faq-item">
                    <h3>How long does it take to get printed tickets or wristbands?</h3>
                    <p>Printed tickets are ready for collection at least 3 days before your event. Wristbands require at least 7 days lead time from order confirmation. Both require full payment before production begins.</p>
                </div>

                <div class="ct-faq-item">
                    <h3>Do you offer support on the day of the event?</h3>
                    <p>Yes. If you have booked our gate operations team, they will be on site throughout the event. For online ticketing clients, our support line is available on the event day. Include your event date in your message so we can prepare.</p>
                </div>

            </div>
        </div>
    </section>

</div>

<style>
    .ct-page {
        padding-top: 5.5rem;
        background: #1a0a0e;
        min-height: 100vh;
    }

    .ct-shell {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 1.25rem;
    }

    /* ── Hero ── */
    .ct-hero {
        padding: 4rem 1.25rem 3.5rem;
        text-align: center;
        background: linear-gradient(160deg, #2c0d16 0%, #1a0a0e 60%);
        border-bottom: 1px solid rgba(255,255,255,.07);
    }

    .ct-hero-inner { max-width: 620px; margin: 0 auto; }

    .ct-eyebrow {
        margin: 0 0 .85rem;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .14em;
        color: rgba(192,40,60,.9);
        text-transform: uppercase;
    }

    .ct-heading {
        margin: 0 0 1rem;
        font-size: clamp(1.9rem, 4vw, 2.7rem);
        font-weight: 800;
        line-height: 1.1;
        color: #fff;
    }

    .ct-sub {
        margin: 0;
        font-size: 1rem;
        line-height: 1.65;
        color: rgba(255,255,255,.65);
    }

    /* ── Body layout ── */
    .ct-body { padding: 4.5rem 0; }

    .ct-layout {
        display: grid;
        grid-template-columns: 1fr 1.4fr;
        gap: 3.5rem;
        align-items: start;
    }

    /* ── Info column ── */
    .ct-info { display: flex; flex-direction: column; gap: 2.25rem; }

    .ct-info-block {}

    .ct-info-title {
        margin: 0 0 .75rem;
        font-size: 1.05rem;
        font-weight: 800;
        color: #fff;
    }

    .ct-info-text {
        margin: 0;
        font-size: .9rem;
        line-height: 1.7;
        color: rgba(255,255,255,.58);
    }

    .ct-details { display: flex; flex-direction: column; gap: .75rem; }

    .ct-detail-row {
        display: flex;
        align-items: center;
        gap: .75rem;
        font-size: .9rem;
        color: rgba(255,255,255,.65);
    }

    .ct-detail-row svg {
        width: 1.1rem;
        height: 1.1rem;
        stroke: #c0283c;
        flex-shrink: 0;
    }

    .ct-detail-row a {
        color: rgba(255,255,255,.65);
        text-decoration: none;
    }

    .ct-detail-row a:hover { color: #fff; }

    /* ── Form card ── */
    .ct-form-card {
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.09);
        border-radius: 20px;
        padding: 2.25rem 2rem;
    }

    .ct-form-title {
        margin: 0 0 1.75rem;
        font-size: 1.15rem;
        font-weight: 800;
        color: #fff;
    }

    .ct-fields {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem 1.25rem;
        margin-bottom: 1.25rem;
    }

    .ct-field { display: flex; flex-direction: column; gap: .38rem; }
    .ct-field--half {}
    .ct-field--full { grid-column: 1 / -1; }

    .ct-field label {
        font-size: .78rem;
        font-weight: 700;
        color: rgba(255,255,255,.65);
        letter-spacing: .02em;
    }

    .ct-req { color: #ff8096; }

    .ct-field input,
    .ct-field select,
    .ct-field textarea {
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.12);
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

    .ct-field input::placeholder,
    .ct-field textarea::placeholder { color: rgba(255,255,255,.25); }

    .ct-field input:focus,
    .ct-field select:focus,
    .ct-field textarea:focus {
        border-color: #c0283c;
        box-shadow: 0 0 0 3px rgba(192,40,60,.18);
    }

    .ct-field input.is-invalid,
    .ct-field select.is-invalid,
    .ct-field textarea.is-invalid {
        border-color: #f87171;
        box-shadow: 0 0 0 3px rgba(248,113,113,.15);
    }

    .ct-field select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='rgba(255,255,255,0.4)' stroke-width='2' stroke-linecap='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .75rem center;
        background-size: 1rem;
        padding-right: 2.4rem;
        cursor: pointer;
    }

    .ct-field select option { background: #1a0a0e; color: #fff; }
    .ct-field textarea { resize: vertical; min-height: 120px; }
    .ct-field-err { font-size: .74rem; color: #f87171; min-height: 1em; }

    .ct-form-error {
        background: rgba(248,113,113,.1);
        border: 1px solid rgba(248,113,113,.28);
        border-radius: 10px;
        padding: .75rem 1rem;
        font-size: .85rem;
        color: #fca5a5;
        margin-bottom: 1rem;
        line-height: 1.5;
    }
    .ct-form-error a { color: #fca5a5; }

    .ct-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        width: 100%;
        padding: .8rem 1.5rem;
        border-radius: 10px;
        border: none;
        background: #1255c0;
        color: #fff;
        font-size: .95rem;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        transition: background .15s, transform .15s;
        box-shadow: 0 4px 16px rgba(18,85,192,.3);
    }
    .ct-submit:hover:not(:disabled) { background: #0e3fa0; transform: translateY(-1px); }
    .ct-submit:disabled { opacity: .6; cursor: not-allowed; }

    @keyframes ctSpin { to { transform: rotate(360deg); } }
    .ct-spinner { width: 1rem; height: 1rem; animation: ctSpin .7s linear infinite; }

    .ct-success-wrap {
        text-align: center;
        padding: 2rem 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .85rem;
    }
    .ct-success-icon {
        width: 3.5rem; height: 3.5rem;
        border-radius: 50%;
        background: rgba(34,197,94,.1);
        border: 1.5px solid rgba(34,197,94,.3);
        display: flex; align-items: center; justify-content: center;
    }
    .ct-success-icon svg { width: 1.75rem; height: 1.75rem; stroke: #22c55e; }
    .ct-success-wrap h3 { margin: 0; font-size: 1.2rem; font-weight: 800; color: #fff; }
    .ct-success-wrap p { margin: 0; font-size: .9rem; color: rgba(255,255,255,.55); line-height: 1.6; max-width: 32ch; }

    /* ── FAQ ── */
    .ct-faq {
        padding: 4.5rem 0 6rem;
        border-top: 1px solid rgba(255,255,255,.06);
    }

    .ct-faq-title {
        text-align: center;
        margin: 0 0 3rem;
        font-size: clamp(1.4rem, 2.5vw, 1.85rem);
        font-weight: 800;
        color: #fff;
    }

    .ct-faq-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem 3rem;
    }

    .ct-faq-item {
        padding-top: 1.25rem;
        border-top: 1px solid rgba(255,255,255,.08);
    }

    .ct-faq-item h3 {
        margin: 0 0 .6rem;
        font-size: .97rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.35;
    }

    .ct-faq-item p {
        margin: 0;
        font-size: .875rem;
        line-height: 1.7;
        color: rgba(255,255,255,.55);
    }

    /* ── Responsive ── */
    @media (max-width: 900px) {
        .ct-layout { grid-template-columns: 1fr; gap: 2.5rem; }
        .ct-faq-grid { grid-template-columns: 1fr; gap: 1.25rem; }
    }

    @media (max-width: 600px) {
        .ct-page { padding-top: 4rem; }
        .ct-hero { padding: 2.75rem 1rem 2.25rem; }
        .ct-body { padding: 2.75rem 0; }
        .ct-form-card { padding: 1.5rem 1.1rem; border-radius: 14px; }
        .ct-fields { grid-template-columns: 1fr; }
        .ct-field--full { grid-column: 1; }
        .ct-faq { padding: 3rem 0 4rem; }
        .ct-faq-title { margin-bottom: 2rem; }
    }
</style>

<script>
(() => {
    const form       = document.getElementById('ct-form');
    const formCard   = document.getElementById('ct-success');
    const submitBtn  = document.getElementById('ct-submit');
    const submitTxt  = document.getElementById('ct-submit-text');
    const submitSpin = document.getElementById('ct-submit-spinner');
    const formErr    = document.getElementById('ct-form-error');

    function setSubmitting(on) {
        submitBtn.disabled = on;
        submitTxt.hidden   = on;
        submitSpin.hidden  = !on;
    }

    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.ct-field-err').forEach(el => el.textContent = '');
    }

    function showError(name, msg) {
        const input = form.querySelector(`[name="${name}"]`);
        const errEl = document.getElementById(`ct-err-${name}`);
        if (input) input.classList.add('is-invalid');
        if (errEl) errEl.textContent = msg;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErrors();
        formErr.hidden = true;

        const data = new FormData(form);
        let hasError = false;

        if (!data.get('name').trim())    { showError('name',    'Please enter your name.');          hasError = true; }
        if (!data.get('email').trim())   { showError('email',   'Please enter your email address.'); hasError = true; }
        if (!data.get('package').trim()) { showError('package', 'Please select a topic.');           hasError = true; }
        if (!data.get('message').trim()) { showError('message', 'Please enter your message.');       hasError = true; }
        if (hasError) return;

        setSubmitting(true);

        try {
            const res  = await fetch('{{ route('contact.send') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': data.get('_token'), 'Accept': 'application/json' },
                body: data,
            });
            const json = await res.json();

            if (res.status === 422 && json.errors) {
                Object.entries(json.errors).forEach(([field, msgs]) => showError(field, msgs[0]));
                setSubmitting(false);
                return;
            }
            if (!res.ok) throw new Error('Server error');

            form.hidden      = true;
            formCard.hidden  = false;
        } catch {
            formErr.hidden = false;
            setSubmitting(false);
        }
    });
})();
</script>

@endsection
