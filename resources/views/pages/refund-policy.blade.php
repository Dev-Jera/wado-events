@extends('layouts.app')

@section('title', 'Refund & Returns Policy — WADO Ticketing')

@section('content')
<div class="policy-page">

    <div class="policy-hero">
        <div class="policy-hero-inner">
            <p class="policy-eyebrow">LEGAL</p>
            <h1 class="policy-heading">Refund &amp; Returns Policy</h1>
            <p class="policy-sub">Last updated: {{ date('d F Y') }}</p>
        </div>
    </div>

    <div class="policy-shell">

        <aside class="policy-toc">
            <p class="policy-toc-title">On this page</p>
            <nav>
                <a href="#general">General Principle</a>
                <a href="#eligible">Eligible Refunds</a>
                <a href="#not-eligible">Non-Eligible Situations</a>
                <a href="#how">How to Request</a>
                <a href="#processing">Processing Time</a>
                <a href="#cancelled-events">Cancelled Events</a>
                <a href="#disputes">Disputes</a>
                <a href="#contact">Contact</a>
            </nav>
        </aside>

        <article class="policy-body">

            <section id="general">
                <h2>1. General Principle</h2>
                <p>All ticket sales on WADO Ticketing are generally <strong>final and non-refundable</strong> once a payment has been confirmed and a ticket has been issued. This is consistent with standard practice in the events industry.</p>
                <p>However, we understand that circumstances can change. We assess refund requests on a case-by-case basis within the criteria below.</p>
            </section>

            <section id="eligible">
                <h2>2. Situations Eligible for a Refund</h2>
                <p>A full or partial refund may be granted in the following situations:</p>
                <ul>
                    <li><strong>Event cancelled by the organiser</strong> — If the event is officially cancelled and not rescheduled, a refund will be issued once the organiser authorises it.</li>
                    <li><strong>Event postponed (non-acceptance)</strong> — If an event is postponed to a new date and you are unable to attend the new date, you may request a refund within 5 days of the postponement announcement.</li>
                    <li><strong>Duplicate charge</strong> — If your payment was processed more than once for the same order due to a technical error, the duplicate charge will be refunded promptly.</li>
                    <li><strong>Ticket not received</strong> — If your ticket was not delivered after a confirmed payment and we are unable to reissue it, a refund will be processed.</li>
                </ul>
            </section>

            <section id="not-eligible">
                <h2>3. Situations Not Eligible for a Refund</h2>
                <p>Refunds will not be granted in the following cases:</p>
                <ul>
                    <li>Change of mind after purchase.</li>
                    <li>Failure to attend the event for any personal reason.</li>
                    <li>Arriving late and missing entry.</li>
                    <li>Entry refused due to violation of event rules (e.g., age restrictions, dress code).</li>
                    <li>Tickets purchased from unofficial or secondary resale sources.</li>
                    <li>Requests made outside the eligible window.</li>
                </ul>
            </section>

            <section id="how">
                <h2>4. How to Request a Refund</h2>
                <p>To submit a refund request:</p>
                <ul>
                    <li>Email us at <a href="mailto:wadoconcepts@gmail.com">wadoconcepts@gmail.com</a> with the subject line: <em>"Refund Request – [Your Order Number]"</em>.</li>
                    <li>Include your full name, registered email address, order number, and reason for the request.</li>
                    <li>Attach any supporting documentation if applicable (e.g., screenshot of duplicate charge).</li>
                </ul>
                <p>Requests must be submitted at least <strong>48 hours before the event start time</strong> unless the refund reason occurs on the day of or after the event (e.g., cancellation notice).</p>
            </section>

            <section id="processing">
                <h2>5. Processing Time</h2>
                <p>Once a refund is approved:</p>
                <ul>
                    <li>Mobile Money refunds are processed within <strong>3 – 5 business days</strong>.</li>
                    <li>You will receive a confirmation email when the refund is initiated.</li>
                    <li>WADO service fees are non-refundable unless the refund is due to our error.</li>
                </ul>
                <p>If you have not received your refund after 7 business days, please contact us.</p>
            </section>

            <section id="cancelled-events">
                <h2>6. Cancelled or Significantly Changed Events</h2>
                <p>WADO acts as a ticketing agent on behalf of event organisers. For cancelled events:</p>
                <ul>
                    <li>We will communicate the refund process to all affected ticket holders via email.</li>
                    <li>The timing and availability of refunds depends on the organiser's decision and their ability to fund returns.</li>
                    <li>Where WADO has collected funds on behalf of the organiser and the organiser approves refunds, we will process them promptly.</li>
                </ul>
                <p>If an organiser is unresponsive or unable to fund refunds, WADO will engage to mediate, but we cannot guarantee repayment from organiser funds we do not hold.</p>
            </section>

            <section id="disputes">
                <h2>7. Disputes</h2>
                <p>If you are not satisfied with our response to your refund request, you may escalate your complaint by emailing <a href="mailto:wadoconcepts@gmail.com">wadoconcepts@gmail.com</a> with "Escalation" in the subject line. We will review your case within 5 business days and provide a final decision.</p>
                <p>All disputes are governed by the laws of Uganda.</p>
            </section>

            <section id="contact">
                <h2>8. Contact</h2>
                <p>For all refund enquiries:</p>
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
