@extends('layouts.app')

@section('title', 'Ticket Policy — WADO Ticketing')

@section('content')
<div class="policy-page">

    <div class="policy-hero">
        <div class="policy-hero-inner">
            <h1 class="policy-heading">Ticket Policy</h1>
            <p class="policy-sub">Last Updated: {{ date('d/m/Y') }}</p>
        </div>
    </div>

    <div class="policy-shell">

        <aside class="policy-toc">
            <p class="policy-toc-title">On this page</p>
            <nav>
                <a href="#validity">Ticket Validity</a>
                <a href="#digital">Digital Tickets</a>
                <a href="#entry">Entry Requirements</a>
                <a href="#transfer">Transfer &amp; Resale</a>
                <a href="#cancellation">Event Cancellation</a>
                <a href="#lost">Lost Tickets</a>
                <a href="#fraud">Fraud Prevention</a>
                <a href="#contact">Contact</a>
            </nav>
        </aside>

        <article class="policy-body">

            <section id="validity">
                <h2>1. Ticket Validity</h2>
                <p>A ticket purchased through WADO Ticketing grants the named holder a single right of entry to the specified event on the stated date and time. Each ticket is uniquely identified and can only be scanned once. Attempting to re-use a ticket after it has been scanned will result in denial of entry.</p>
                <p>Tickets are only valid for the exact event, date, and category for which they were purchased. No substitution or inter-event transfer is permitted.</p>
            </section>

            <section id="digital">
                <h2>2. Digital Tickets</h2>
                <p>All tickets issued through WADO Ticketing are digital. Upon successful payment:</p>
                <ul>
                    <li>A QR-coded ticket is sent to the email address you registered with.</li>
                    <li>Tickets are also accessible via your account dashboard under "My Tickets".</li>
                    <li>You may present your ticket on a mobile device or as a printed copy at the gate.</li>
                </ul>
                <p>Ensure your device screen is bright enough and the QR code is clearly visible to the scanner. Blurry or obscured codes may be rejected.</p>
            </section>

            <section id="entry">
                <h2>3. Entry Requirements</h2>
                <p>At the event gate:</p>
                <ul>
                    <li>Present your QR code (digital or printed) for scanning by our gate staff.</li>
                    <li>Gate staff may request a valid national ID or passport to verify identity on high-security events.</li>
                    <li>Tickets that appear tampered with or altered will be refused.</li>
                    <li>Entry may be refused on grounds of safety, venue capacity, or breach of event rules.</li>
                </ul>
                <p>Arriving early is recommended. WADO is not responsible for delays caused by queues, traffic, or other factors outside our control.</p>
            </section>

            <section id="transfer">
                <h2>4. Transfer &amp; Resale</h2>
                <p>You may transfer your ticket to another person by updating the ticket holder details in your account dashboard, subject to the following conditions:</p>
                <ul>
                    <li>Transfers are only permitted before the event date.</li>
                    <li>Reselling tickets at a price above face value ("touting") is strictly prohibited.</li>
                    <li>WADO reserves the right to cancel any ticket found to be listed for above-face-value resale.</li>
                    <li>Tickets purchased through unofficial resale channels are not guaranteed valid and will not be replaced if refused entry.</li>
                </ul>
            </section>

            <section id="cancellation">
                <h2>5. Event Cancellation or Postponement</h2>
                <p>In the event that an event is cancelled or significantly changed by the organiser:</p>
                <ul>
                    <li>We will notify you via the email address on your account as soon as we are informed.</li>
                    <li>Where the organiser authorises refunds, these will be processed in accordance with our <a href="{{ route('refund-policy') }}">Refund &amp; Returns Policy</a>.</li>
                    <li>For postponed events, your ticket will automatically remain valid for the new date unless you opt for a refund within the stated window.</li>
                </ul>
                <p>WADO acts as the ticketing agent only; refund decisions for cancelled events ultimately rest with the event organiser.</p>
            </section>

            <section id="lost">
                <h2>6. Lost or Inaccessible Tickets</h2>
                <p>If you cannot access your ticket email:</p>
                <ul>
                    <li>Log in to your WADO account and visit "My Tickets" to retrieve and re-send your ticket.</li>
                    <li>Contact us at <a href="mailto:wadoconcepts@gmail.com">wadoconcepts@gmail.com</a> with your name and order details for assistance.</li>
                </ul>
                <p>WADO is not liable for tickets lost due to an incorrect email address provided at registration, or for tickets forwarded to third parties without your consent.</p>
            </section>

            <section id="fraud">
                <h2>7. Fraud Prevention</h2>
                <p>Forged, duplicated, or unauthorised tickets are void and will be refused at the gate. WADO employs cryptographic QR verification to detect counterfeit tickets. Any attempt to:</p>
                <ul>
                    <li>Generate, duplicate, or alter ticket QR codes;</li>
                    <li>Bypass or interfere with the scanning system;</li>
                    <li>Purchase tickets using fraudulent payment credentials</li>
                </ul>
                <p>will result in account termination and may be reported to the appropriate authorities.</p>
            </section>

            <section id="contact">
                <h2>8. Contact</h2>
                <p>For any questions about your ticket, contact our support team:</p>
                <ul>
                    <li>Email: <a href="mailto:wadoconcepts@gmail.com">wadoconcepts@gmail.com</a></li>
                    <li>Phone: <a href="tel:+256703015846">0703 015 846</a></li>
                    <li>Hours: Monday – Friday, 9am – 6pm EAT</li>
                </ul>
            </section>

        </article>
    </div>
</div>
@endsection
