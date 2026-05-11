@extends('layouts.app')

@section('title', 'Terms & Conditions — WADO Ticketing')

@section('content')
<div class="policy-page">

    <div class="policy-hero">
        <div class="policy-hero-inner">
            <p class="policy-eyebrow">LEGAL</p>
            <h1 class="policy-heading">Terms &amp; Conditions</h1>
            <p class="policy-sub">Last updated: {{ date('d F Y') }}</p>
        </div>
    </div>

    <div class="policy-shell">

        <aside class="policy-toc">
            <p class="policy-toc-title">On this page</p>
            <nav>
                <a href="#acceptance">Acceptance of Terms</a>
                <a href="#services">Our Services</a>
                <a href="#accounts">Accounts</a>
                <a href="#tickets">Tickets &amp; Purchases</a>
                <a href="#payments">Payments</a>
                <a href="#conduct">Acceptable Use</a>
                <a href="#intellectual-property">Intellectual Property</a>
                <a href="#liability">Limitation of Liability</a>
                <a href="#changes">Changes to Terms</a>
                <a href="#contact">Contact</a>
            </nav>
        </aside>

        <article class="policy-body">

            <section id="acceptance">
                <h2>1. Acceptance of Terms</h2>
                <p>By accessing or using the WADO Ticketing platform (the "Service") — including our website and any associated applications — you agree to be bound by these Terms &amp; Conditions ("Terms"). If you do not agree to these Terms, you may not use the Service.</p>
                <p>These Terms constitute a legally binding agreement between you ("User") and WADO / Lico Holdings Limited ("we," "us," or "our"), a company registered in Uganda.</p>
            </section>

            <section id="services">
                <h2>2. Our Services</h2>
                <p>WADO Ticketing provides an online platform for event organisers to list events and sell tickets, and for attendees to discover, purchase, and manage tickets to those events. We act as an intermediary between event organisers and ticket buyers.</p>
                <ul>
                    <li>We are not the organiser of any event listed on our platform unless explicitly stated.</li>
                    <li>Event details, including dates, venues, and pricing, are set by the respective organisers.</li>
                    <li>We reserve the right to remove any event listing that violates these Terms or applicable law.</li>
                </ul>
            </section>

            <section id="accounts">
                <h2>3. Accounts</h2>
                <p>To purchase tickets you must create an account. You agree to:</p>
                <ul>
                    <li>Provide accurate and current information during registration.</li>
                    <li>Keep your password confidential and not share it with others.</li>
                    <li>Notify us immediately at <a href="mailto:wadoconcepts@gmail.com">wadoconcepts@gmail.com</a> if you suspect unauthorised access to your account.</li>
                    <li>Take responsibility for all activities that occur under your account.</li>
                </ul>
                <p>We reserve the right to suspend or terminate accounts that violate these Terms.</p>
            </section>

            <section id="tickets">
                <h2>4. Tickets &amp; Purchases</h2>
                <p>All ticket sales are subject to availability. When you purchase a ticket:</p>
                <ul>
                    <li>A QR-coded ticket is issued to the email address associated with your account.</li>
                    <li>Each ticket is for single-use entry only. Duplicate or forged tickets are invalid and will be refused.</li>
                    <li>Tickets may not be resold or transferred for commercial gain without prior written consent from WADO.</li>
                    <li>We are not responsible for tickets lost, stolen, or sold through unauthorised channels.</li>
                </ul>
                <p>Please review our <a href="{{ route('ticket-policy') }}">Ticket Policy</a> for full details on ticket usage, validity, and entry requirements.</p>
            </section>

            <section id="payments">
                <h2>5. Payments</h2>
                <p>All payments are processed in Ugandan Shillings (UGX) via our supported payment providers (currently Mobile Money). By making a payment you confirm:</p>
                <ul>
                    <li>You are authorised to use the payment method provided.</li>
                    <li>All payment details are accurate and complete.</li>
                </ul>
                <p>Pricing is set by event organisers. WADO may add a service fee to the ticket price. All applicable fees are displayed before checkout confirmation.</p>
                <p>For information on refunds and cancellations, see our <a href="{{ route('refund-policy') }}">Refund &amp; Returns Policy</a>.</p>
            </section>

            <section id="conduct">
                <h2>6. Acceptable Use</h2>
                <p>You agree not to:</p>
                <ul>
                    <li>Attempt to circumvent, hack, or manipulate the ticketing or payment systems.</li>
                    <li>Create accounts with false or misleading information.</li>
                    <li>Purchase tickets using fraudulent payment credentials.</li>
                    <li>Scrape, crawl, or otherwise harvest data from the platform without permission.</li>
                    <li>Upload or transmit any virus, malware, or malicious code.</li>
                    <li>Engage in any activity that disrupts or damages the Service.</li>
                </ul>
                <p>Violation of these rules may result in immediate account suspension and, where appropriate, referral to the relevant authorities.</p>
            </section>

            <section id="intellectual-property">
                <h2>7. Intellectual Property</h2>
                <p>All content on the WADO Ticketing platform — including logos, graphics, text, and software — is owned by or licensed to WADO / Lico Holdings Limited and is protected by applicable intellectual property laws. You may not reproduce, distribute, or create derivative works without our express written permission.</p>
            </section>

            <section id="liability">
                <h2>8. Limitation of Liability</h2>
                <p>To the fullest extent permitted by Ugandan law, WADO shall not be liable for:</p>
                <ul>
                    <li>Event cancellations, postponements, or changes made by the organiser.</li>
                    <li>Loss or damage arising from your use of, or inability to use, the Service.</li>
                    <li>Any indirect, incidental, or consequential damages.</li>
                </ul>
                <p>Our total liability to you for any claim shall not exceed the amount you paid for the relevant ticket(s).</p>
            </section>

            <section id="changes">
                <h2>9. Changes to These Terms</h2>
                <p>We may update these Terms from time to time. The revised version will be posted on this page with an updated date. Continued use of the Service after changes are posted constitutes your acceptance of the updated Terms.</p>
            </section>

            <section id="contact">
                <h2>10. Contact Us</h2>
                <p>If you have questions about these Terms, please contact us:</p>
                <ul>
                    <li>Email: <a href="mailto:wadoconcepts@gmail.com">wadoconcepts@gmail.com</a></li>
                    <li>Phone: <a href="tel:+256703015846">0703 015 846</a></li>
                    <li>Address: Kireka, opposite Shell, Lico Holdings Limited building, Kampala, Uganda</li>
                </ul>
            </section>

        </article>
    </div>
</div>

@endsection
