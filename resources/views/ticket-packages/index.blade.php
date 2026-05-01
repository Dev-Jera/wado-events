@extends('layouts.app')

@section('content')

<div class="tp-page">

    {{-- ── Hero ── --}}
    <section class="tp-hero">
        <div class="tp-hero-inner">
            <p class="tp-eyebrow">FOR EVENT ORGANISERS</p>
            <h1 class="tp-heading">Ticket Packages &amp; Pricing</h1>
            <p class="tp-sub">Three ways to work with us. Every package is built around one goal: making your event run without a hitch, from first sale to last guest through the gate.</p>
        </div>
    </section>

    {{--
        ADMIN NOTE — Online Ticketing pricing rationale:
        6% is justified because digital tickets have zero production cost — our value is the
        platform, payment rails, QR generation, and delivery infrastructure. The 1,000 UGX
        floor protects us on low-ticket-price events (e.g. 5,000 UGX × 6% = 300 UGX which
        doesn't cover transaction costs). Gate team is billed as a fixed event fee by scale
        (not per-ticket) because it's skilled labour + equipment — variable per-ticket billing
        would confuse organisers and doesn't reflect actual cost structure. SMS is a flat
        add-on because we absorb provider costs inside it at small-to-medium volumes.
    --}}
    <section class="tp-pkg" id="pkg-online">
        <div class="tp-shell">
            <div class="tp-pkg-grid">
                <div class="tp-pkg-media">
                    <img src="{{ asset('images/Online ticket.jpg') }}" alt="Online ticketing">
                    <div class="tp-pkg-media-overlay"></div>
                    <div class="tp-pkg-media-badge">Most Popular</div>
                </div>
                <div class="tp-pkg-body">
                    <span class="tp-pkg-tag tp-pkg-tag--blue">Online Ticketing</span>
                    <h2 class="tp-pkg-title">Sell tickets online.<br>We handle everything else.</h2>
                    <p class="tp-pkg-tagline">Customers buy from their phone, tickets land in their inbox, and your sales dashboard updates in real time. You just show up on event day.</p>

                    <div class="tp-section-label">What we provide</div>
                    <div class="tp-feat-grid">
                        <div class="tp-feat-item">Custom event listing on the WADO platform</div>
                        <div class="tp-feat-item">Secure payment processing via MTN, Airtel and card</div>
                        <div class="tp-feat-item">QR-coded digital tickets delivered to each buyer's email</div>
                        <div class="tp-feat-item">Real-time sales dashboard for the organiser</div>
                        <div class="tp-feat-item">Multiple ticket tiers including VIP, General and Early Bird</div>
                        <div class="tp-feat-item">Automatic capacity limits and sold-out management</div>
                        <div class="tp-feat-item">Refund and cancellation handling</div>
                        <div class="tp-feat-item tp-feat-item--opt">Gate verification team on event day <span class="tp-opt-tag">on request</span></div>
                    </div>

                    <div class="tp-section-label">Your part</div>
                    <p class="tp-role-prose">
                        Share your event details with us: name, date, venue, poster image, and ticket tiers. Promote your booking link to your audience. If you need our gate team on the day, let us know at least 5 days before the event.
                    </p>

                    <div class="tp-fee-footer">
                        <div class="tp-fee-main">
                            <span class="tp-fee-label">Service fee</span>
                            <span class="tp-fee-value">6% per ticket sold <span class="tp-fee-min">· min. 1,000 UGX per ticket</span></span>
                        </div>
                        <div class="tp-fee-extras">Gate operations team from 200,000 UGX &nbsp;·&nbsp; SMS delivery 50,000 UGX flat</div>
                    </div>

                    <button type="button" class="tp-pkg-cta"
                            data-package="Online Ticketing &amp; Event Management"
                            onclick="openEnquiry(this.dataset.package)">
                        Get in touch
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{--
        ADMIN NOTE — Printed Tickets pricing rationale:
        Printing is a direct production cost — ink, paper stock, QR encoding, and labour.
        500 UGX (standard) covers production at thin margin; 800 UGX (premium) reflects
        better stock and finish. The 3% system fee covers the verification infrastructure
        (serial database, scanner app, dashboard) which we build and maintain. Minimum
        order of 100 ensures it's worth setting up the print run. Gate staff is the same
        fixed-fee model as online — it's a separate staffing cost, not a production cost.
    --}}
    <section class="tp-pkg tp-pkg--alt" id="pkg-printed">
        <div class="tp-shell">
            <div class="tp-pkg-grid">
                <div class="tp-pkg-media">
                    <img src="{{ asset('images/cutout-ticket.jpg') }}" alt="Batch printed tickets">
                    <div class="tp-pkg-media-overlay"></div>
                </div>
                <div class="tp-pkg-body">
                    <span class="tp-pkg-tag tp-pkg-tag--maroon">Printed Tickets</span>
                    <h2 class="tp-pkg-title">Physical tickets, printed<br>and ready before your event.</h2>
                    <p class="tp-pkg-tagline">Bulk ticket batches with unique QR codes and serial numbers. Ideal for walk-in audiences, gate sales, and events where a physical ticket is part of the experience.</p>

                    <div class="tp-section-label">What we provide</div>
                    <div class="tp-feat-grid">
                        <div class="tp-feat-item">Professional ticket design, or production from your own artwork</div>
                        <div class="tp-feat-item">Unique serial numbers and QR codes on every ticket</div>
                        <div class="tp-feat-item">High-quality print on counterfeit-resistant stock</div>
                        <div class="tp-feat-item">Standard (mono) or premium (full-colour, gloss) finish</div>
                        <div class="tp-feat-item">Tickets ready for collection at least 3 days before the event</div>
                        <div class="tp-feat-item tp-feat-item--opt">QR scanner system for gate control <span class="tp-opt-tag">on request</span></div>
                        <div class="tp-feat-item tp-feat-item--opt">Gate verification staff on event day <span class="tp-opt-tag">on request</span></div>
                    </div>

                    <div class="tp-section-label">Your part</div>
                    <p class="tp-role-prose">
                        Confirm your ticket quantity, preferred finish, and any design notes. Collect the printed tickets at least 3 days before the event and handle distribution to your sellers or gate agents. Let us know in advance if you want the scanner system set up.
                    </p>

                    <div class="tp-fee-footer tp-fee-footer--maroon">
                        <div class="tp-fee-main">
                            <span class="tp-fee-label">Service fee</span>
                            <span class="tp-fee-value">From 500 UGX per ticket <span class="tp-fee-min">· 3% system fee on batch · min. 100 tickets</span></span>
                        </div>
                        <div class="tp-fee-extras">Gate operations team from 200,000 UGX</div>
                    </div>

                    <button type="button" class="tp-pkg-cta tp-pkg-cta--maroon"
                            data-package="Gate-Sale Ticket Printing"
                            onclick="openEnquiry(this.dataset.package)">
                        Get in touch
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{--
        ADMIN NOTE — Wristband pricing rationale:
        Wristbands are a physical product with real material and production costs — cloth
        wristbands (1,500 UGX) cover material + QR encoding + labour at a reasonable margin;
        tyvek (2,500 UGX) reflects better tamper-proof stock and snap-lock hardware. The 5%
        platform fee covers the entry control system, scanning infrastructure, and dashboard
        — this is higher than printed tickets (3%) because wristband events typically demand
        more sophisticated access control setup. Gate operations for wristband events is priced
        slightly higher than standard gate teams because these events are usually larger and
        higher-security, requiring more staff coordination and equipment. Payment in full before
        production protects us against cancellations on physical orders we cannot recover.
    --}}
    <section class="tp-pkg" id="pkg-wristband">
        <div class="tp-shell">
            <div class="tp-pkg-grid">
                <div class="tp-pkg-media">
                    <img src="{{ asset('images/wrist-ticket.jpg') }}" alt="VIP wristband tickets">
                    <div class="tp-pkg-media-overlay"></div>
                    <div class="tp-pkg-media-badge tp-pkg-media-badge--gold">Premium</div>
                </div>
                <div class="tp-pkg-body">
                    <span class="tp-pkg-tag tp-pkg-tag--gold">VIP Wristbands</span>
                    <h2 class="tp-pkg-title">Premium access control<br>for high-security events.</h2>
                    <p class="tp-pkg-tagline">Wristbands signal the standard of your event before a single word is spoken at the gate. QR-encoded, tamper-proof, and colour-coded by access tier.</p>

                    <div class="tp-section-label">What we provide</div>
                    <div class="tp-feat-grid">
                        <div class="tp-feat-item">Professional wristband production in cloth or tyvek</div>
                        <div class="tp-feat-item">Unique QR code encoding on every wristband</div>
                        <div class="tp-feat-item">Colour-coding for each access tier: VIP, General, Staff and Press</div>
                        <div class="tp-feat-item">Anti-tamper snap-lock closure on tyvek wristbands</div>
                        <div class="tp-feat-item">Entry control system and gate scanning setup</div>
                        <div class="tp-feat-item">Re-entry tracking and blacklist management</div>
                        <div class="tp-feat-item tp-feat-item--opt">Full gate operations team with live dashboard <span class="tp-opt-tag">on request</span></div>
                        <div class="tp-feat-item tp-feat-item--soon">RFID / NFC wristbands <span class="tp-opt-tag tp-opt-tag--soon">coming soon</span></div>
                    </div>

                    <div class="tp-section-label">Your part</div>
                    <p class="tp-role-prose">
                        Confirm your wristband count, type, and colour zones at least 7 days before the event. Note that production only begins after full payment is received. Define your access tiers clearly so we can colour-code correctly, and if you are using our gate operations team, coordinate the gate layout with us at least 48 hours before.
                    </p>

                    <div class="tp-fee-footer tp-fee-footer--gold">
                        <div class="tp-fee-main">
                            <span class="tp-fee-label">Service fee</span>
                            <span class="tp-fee-value">From 1,500 UGX per wristband <span class="tp-fee-min">· 5% platform fee on total order</span></span>
                        </div>
                        <div class="tp-fee-extras">Gate operations team from 300,000 UGX · Full payment required before production</div>
                    </div>

                    <button type="button" class="tp-pkg-cta tp-pkg-cta--gold"
                            data-package="VIP Wristband Tickets"
                            onclick="openEnquiry(this.dataset.package)">
                        Get in touch
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Add-ons summary ── --}}
    <section class="tp-addons">
        <div class="tp-shell">
            <p class="tp-eyebrow" style="text-align:center;margin-bottom:.75rem">AVAILABLE FOR ALL PACKAGES</p>
            <h2 class="tp-addons-title">Optional add-ons</h2>
            <div class="tp-addons-grid">
                <div class="tp-addon-card">
                    <div class="tp-addon-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.7 12a19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.78a16 16 0 0 0 6.29 6.29l1.08-1.08a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    <h3 class="tp-addon-name">SMS Ticket Delivery</h3>
                    <p class="tp-addon-desc">Send tickets directly to buyers' phones via SMS for guests who can't access email.</p>
                    <span class="tp-addon-price">50,000 UGX / event</span>
                </div>
                <div class="tp-addon-card">
                    <div class="tp-addon-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    </div>
                    <h3 class="tp-addon-name">Event Promotion Listing</h3>
                    <p class="tp-addon-desc">Priority featured placement on the WADO homepage, pushed to our audience and social channels.</p>
                    <span class="tp-addon-price">Contact us for rates</span>
                </div>
                <div class="tp-addon-card">
                    <div class="tp-addon-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3 class="tp-addon-name">Full Gate Operations</h3>
                    <p class="tp-addon-desc">Our trained staff run your entire gate operation: scanners, access logs, crowd control and live reporting.</p>
                    <span class="tp-addon-price">From 200,000 UGX / event</span>
                </div>
                <div class="tp-addon-card">
                    <div class="tp-addon-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <h3 class="tp-addon-name">Live Sales Dashboard</h3>
                    <p class="tp-addon-desc">Real-time revenue, attendance, and scan data on a private dashboard shared with you during the event.</p>
                    <span class="tp-addon-price">Included with online ticketing</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Bottom CTA ── --}}
    <section class="tp-cta">
        <div class="tp-shell">
            <div class="tp-cta-box">
                <h2>Not sure which package suits your event?</h2>
                <p>Reach out and we'll walk you through the best option for your audience size, venue type, and budget. No commitment required.</p>
                <button type="button" class="tp-cta-btn" onclick="openEnquiry()">Talk to us about your event</button>
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
        max-width: 1140px;
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

    .tp-hero-inner { max-width: 700px; margin: 0 auto; }

    .tp-eyebrow {
        margin: 0 0 0.85rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        color: rgba(192,40,60,.9);
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


    /* ── Package sections ── */
    .tp-pkg {
        padding: 5rem 0;
        border-bottom: 1px solid rgba(255,255,255,.06);
    }
    .tp-pkg--alt { background: rgba(255,255,255,.02); }

    .tp-pkg-grid {
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        gap: 0;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.09);
        background: rgba(255,255,255,.025);
    }

    .tp-pkg:nth-child(odd) .tp-pkg-grid { direction: rtl; }
    .tp-pkg:nth-child(odd) .tp-pkg-grid > * { direction: ltr; }

    /* ── Media panel ── */
    .tp-pkg-media {
        position: relative;
        min-height: 520px;
        overflow: hidden;
    }
    .tp-pkg-media img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .6s ease;
    }
    .tp-pkg-grid:hover .tp-pkg-media img { transform: scale(1.04); }
    .tp-pkg-media-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(20,5,12,.55) 0%, rgba(20,5,12,.2) 100%);
    }
    .tp-pkg-media-badge {
        position: absolute;
        top: 1.25rem;
        left: 1.25rem;
        padding: .32rem .85rem;
        border-radius: 999px;
        background: #1255c0;
        color: #fff;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }
    .tp-pkg-media-badge--gold { background: #1255c0; }

    /* ── Body panel ── */
    .tp-pkg-body {
        padding: 2.75rem 2.25rem;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .tp-pkg-tag {
        display: inline-flex;
        align-self: flex-start;
        padding: .28rem .8rem;
        border-radius: 999px;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: .85rem;
    }
    .tp-pkg-tag--blue   { background: rgba(18,85,192,.18);  border: 1px solid rgba(18,85,192,.45);  color: #6ea8fe; }
    .tp-pkg-tag--maroon { background: rgba(192,40,60,.15);  border: 1px solid rgba(192,40,60,.4);   color: #ff8096; }
    .tp-pkg-tag--gold   { background: rgba(192,40,60,.12);  border: 1px solid rgba(192,40,60,.35);  color: #ff8096; }

    .tp-pkg-title {
        margin: 0 0 .7rem;
        font-size: clamp(1.25rem, 2.5vw, 1.7rem);
        font-weight: 800;
        line-height: 1.2;
        color: #fff;
    }
    .tp-pkg-tagline {
        margin: 0 0 1.5rem;
        font-size: .95rem;
        line-height: 1.65;
        color: rgba(255,255,255,.62);
    }

    /* ── Section label ── */
    .tp-section-label {
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: rgba(255,255,255,.35);
        margin: 1.5rem 0 .8rem;
        padding-bottom: .4rem;
        border-bottom: 1px solid rgba(255,255,255,.07);
    }

    /* ── Feature grid ── */
    .tp-feat-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .5rem;
        margin-bottom: .25rem;
    }
    .tp-feat-item {
        font-size: .845rem;
        line-height: 1.45;
        color: rgba(255,255,255,.75);
        background: rgba(255,255,255,.04);
        border-radius: 8px;
        padding: .55rem .75rem;
    }
    .tp-feat-item--opt {
        color: rgba(255,255,255,.42);
        background: rgba(192,40,60,.05);
    }
    .tp-feat-item--soon {
        color: rgba(255,255,255,.25);
        background: rgba(255,255,255,.02);
    }

    .tp-opt-tag {
        font-size: .75rem;
        font-weight: 600;
        color: #1255c0;
        margin-left: .3rem;
        text-decoration: underline;
        text-decoration-style: dotted;
        text-underline-offset: 3px;
    }
    .tp-opt-tag--soon {
        color: rgba(255,255,255,.3);
        text-decoration: none;
    }

    /* ── Role prose ── */
    .tp-role-prose {
        margin: 0;
        font-size: .88rem;
        line-height: 1.65;
        color: rgba(255,255,255,.55);
    }

    /* ── Fee footer ── */
    .tp-fee-footer {
        margin-top: 1.75rem;
        padding: 1.1rem 1.25rem;
        border-radius: 12px;
        background: rgba(18,85,192,.08);
        border: 1px solid rgba(18,85,192,.25);
    }
    .tp-fee-footer--maroon {
        background: rgba(192,40,60,.07);
        border-color: rgba(192,40,60,.22);
    }
    .tp-fee-footer--gold {
        background: rgba(18,85,192,.08);
        border-color: rgba(18,85,192,.25);
    }
    .tp-fee-main {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: .5rem .75rem;
        margin-bottom: .5rem;
    }
    .tp-fee-label {
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: rgba(255,255,255,.4);
        flex-shrink: 0;
    }
    .tp-fee-value {
        font-size: .97rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.35;
    }
    .tp-fee-min {
        font-size: .78rem;
        font-weight: 500;
        color: rgba(255,255,255,.45);
        margin-left: .2rem;
    }
    .tp-fee-extras {
        font-size: .78rem;
        color: rgba(255,255,255,.4);
        line-height: 1.5;
    }

    /* ── CTA button ── */
    .tp-pkg-cta {
        display: inline-flex;
        align-self: flex-start;
        padding: .7rem 1.5rem;
        border-radius: 999px;
        border: none;
        background: #1255c0;
        color: #fff;
        font-size: .9rem;
        font-weight: 700;
        cursor: pointer;
        transition: background .15s, transform .15s;
        margin-top: 1.5rem;
    }
    .tp-pkg-cta:hover { background: #0e3fa0; transform: translateY(-1px); }
    .tp-pkg-cta--maroon { background: #c0283c; }
    .tp-pkg-cta--maroon:hover { background: #a01e2e; }
    .tp-pkg-cta--gold { background: #1255c0; }
    .tp-pkg-cta--gold:hover { background: #0e3fa0; transform: translateY(-1px); }

    /* ── Add-ons section ── */
    .tp-addons {
        padding: 4.5rem 0;
        border-top: 1px solid rgba(255,255,255,.06);
    }
    .tp-addons-title {
        text-align: center;
        margin: 0 0 2.5rem;
        font-size: clamp(1.3rem, 2.5vw, 1.7rem);
        font-weight: 800;
        color: #fff;
    }
    .tp-addons-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
    }
    .tp-addon-card {
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 16px;
        padding: 1.5rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: .7rem;
        transition: border-color .2s;
    }
    .tp-addon-card:hover { border-color: rgba(192,40,60,.4); }
    .tp-addon-icon {
        width: 2.5rem; height: 2.5rem;
        background: rgba(18,85,192,.15);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .tp-addon-icon svg { width: 1.25rem; height: 1.25rem; stroke: #6ea8fe; }
    .tp-addon-name { margin: 0; font-size: .95rem; font-weight: 700; color: #fff; line-height: 1.25; }
    .tp-addon-desc { margin: 0; font-size: .83rem; line-height: 1.55; color: rgba(255,255,255,.55); flex: 1; }
    .tp-addon-price { font-size: .8rem; font-weight: 700; color: #ff8096; }

    /* ── Bottom CTA ── */
    .tp-cta { padding: 3rem 0 5rem; }
    .tp-cta-box {
        background: linear-gradient(130deg, #2c0d16 0%, #1f1030 100%);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 20px;
        padding: 3rem 2.5rem;
        text-align: center;
        display: grid;
        gap: .9rem;
    }
    .tp-cta-box h2 { margin: 0; font-size: clamp(1.3rem, 2.5vw, 1.8rem); font-weight: 800; color: #fff; }
    .tp-cta-box p { margin: 0; color: rgba(255,255,255,.65); font-size: .97rem; line-height: 1.6; max-width: 54ch; margin-inline: auto; }
    .tp-cta-btn {
        display: inline-flex; align-self: center; justify-self: center;
        padding: .75rem 2rem; border-radius: 999px; border: none;
        background: #1255c0; color: #fff; font-size: .95rem; font-weight: 700;
        cursor: pointer; margin-top: .5rem;
        transition: background .15s, transform .15s;
        box-shadow: 0 4px 16px rgba(18,85,192,.35);
    }
    .tp-cta-btn:hover { background: #0e3fa0; transform: translateY(-1px); }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
        .tp-addons-grid { grid-template-columns: repeat(2, 1fr); }
        .tp-pkg-grid { grid-template-columns: 1fr 1fr; }
        .tp-pkg:nth-child(odd) .tp-pkg-grid { direction: ltr; }
    }

    @media (max-width: 780px) {
        .tp-pkg-grid { grid-template-columns: 1fr; border-radius: 16px; }
        .tp-pkg-media { min-height: 260px; }
        .tp-pkg-body { padding: 1.75rem 1.35rem; }
        .tp-pkg { padding: 3rem 0; }
        .tp-pkg-title { font-size: 1.3rem; }
        .tp-cta-box { padding: 2rem 1.5rem; border-radius: 16px; }
    }

    @media (max-width: 580px) {
        .tp-page { padding-top: 4rem; }
        .tp-hero { padding: 2.75rem 1rem 2.25rem; }
        .tp-addons-grid { grid-template-columns: 1fr; }
        .tp-feat-grid { grid-template-columns: 1fr; }
        .tp-pkg-body { padding: 1.35rem 1.1rem; }
        .tp-pkg-cta { width: 100%; justify-content: center; }
        .tp-cta { padding: 2rem 0 3.5rem; }
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
                            <option value="">Select a package</option>
                            <option value="Online Ticketing &amp; Event Management">Online Ticketing &amp; Event Management</option>
                            <option value="Gate-Sale Ticket Printing">Gate-Sale Ticket Printing</option>
                            <option value="VIP Wristband Tickets">VIP Wristband Tickets</option>
                            <option value="Not sure yet">Not sure yet, I need a recommendation</option>
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
    .enq-overlay {
        position: fixed; inset: 0; z-index: 200;
        display: flex; align-items: center; justify-content: center; padding: 1rem;
        opacity: 0; visibility: hidden; pointer-events: none;
        transition: opacity .28s ease, visibility .28s ease;
    }
    .enq-overlay.is-open { opacity: 1; visibility: visible; pointer-events: auto; }
    .enq-backdrop { position: absolute; inset: 0; background: rgba(4,6,14,.80); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }

    .enq-dialog {
        position: relative; z-index: 1; width: min(760px, 100%); max-height: 92vh; overflow-y: auto;
        border-radius: 20px; background: #16080d; border: 1px solid rgba(255,255,255,.1);
        box-shadow: 0 30px 80px rgba(0,0,0,.6), 0 0 0 1px rgba(192,40,60,.15);
        padding: 2.5rem; transform: translateY(20px) scale(.97);
        transition: transform .3s ease, opacity .28s ease;
        scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.1) transparent;
    }
    .enq-overlay.is-open .enq-dialog { transform: translateY(0) scale(1); }

    .enq-close {
        position: absolute; top: 1.1rem; right: 1.1rem; width: 2.1rem; height: 2.1rem;
        border-radius: 999px; border: 1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.07);
        color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: background .15s; flex-shrink: 0;
    }
    .enq-close:hover { background: rgba(192,40,60,.35); }
    .enq-close svg { width: 1rem; height: 1rem; }

    .enq-header { margin-bottom: 1.75rem; padding-right: 2rem; }
    .enq-eyebrow { margin: 0 0 .6rem; font-size: .68rem; font-weight: 700; letter-spacing: .18em; color: rgba(255,160,170,.85); text-transform: uppercase; }
    .enq-title { margin: 0 0 .5rem; font-size: clamp(1.25rem, 3vw, 1.7rem); font-weight: 800; color: #fff; line-height: 1.15; }
    .enq-sub { margin: 0; font-size: .9rem; color: rgba(255,255,255,.55); line-height: 1.55; }

    .enq-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem 1.25rem; margin-bottom: 1.25rem; }
    .enq-field { display: flex; flex-direction: column; gap: .38rem; }
    .enq-field--full { grid-column: 1 / -1; }
    .enq-field label { font-size: .78rem; font-weight: 700; color: rgba(255,255,255,.7); letter-spacing: .03em; }
    .enq-req { color: #ff8096; }

    .enq-field input,
    .enq-field select,
    .enq-field textarea {
        background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.14); border-radius: 10px;
        padding: .65rem .9rem; color: #fff; font-size: .92rem; font-family: inherit; outline: none;
        transition: border-color .18s, box-shadow .18s; width: 100%; box-sizing: border-box;
    }
    .enq-field input::placeholder, .enq-field textarea::placeholder { color: rgba(255,255,255,.28); }
    .enq-field input:focus, .enq-field select:focus, .enq-field textarea:focus { border-color: #c0283c; box-shadow: 0 0 0 3px rgba(192,40,60,.2); }
    .enq-field input.is-invalid, .enq-field select.is-invalid { border-color: #f87171; box-shadow: 0 0 0 3px rgba(248,113,113,.18); }
    .enq-field select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='rgba(255,255,255,0.45)' stroke-width='2' stroke-linecap='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right .75rem center; background-size: 1rem; padding-right: 2.4rem; cursor: pointer;
    }
    .enq-field select option { background: #1a0a0e; color: #fff; }
    .enq-field textarea { resize: vertical; min-height: 100px; }
    .enq-field-err { font-size: .74rem; color: #f87171; min-height: 1em; }

    .enq-form-error { background: rgba(248,113,113,.1); border: 1px solid rgba(248,113,113,.3); border-radius: 10px; padding: .75rem 1rem; font-size: .85rem; color: #fca5a5; margin-bottom: 1.1rem; line-height: 1.5; }
    .enq-form-error a { color: #fca5a5; }

    .enq-submit {
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        width: 100%; padding: .8rem 1.5rem; border-radius: 999px; border: none;
        background: #1255c0; color: #fff; font-size: .95rem; font-weight: 700; font-family: inherit;
        cursor: pointer; box-shadow: 0 4px 16px rgba(18,85,192,.35);
        transition: background .15s, transform .15s, box-shadow .15s;
    }
    .enq-submit:hover:not(:disabled) { background: #0e3fa0; transform: translateY(-1px); box-shadow: 0 6px 22px rgba(18,85,192,.45); }
    .enq-submit:disabled { opacity: .6; cursor: not-allowed; }

    @keyframes enqSpin { to { transform: rotate(360deg); } }
    .enq-spinner { width: 1rem; height: 1rem; animation: enqSpin .7s linear infinite; }

    .enq-success-wrap { display: flex; flex-direction: column; align-items: center; text-align: center; padding: 1.5rem 0 .5rem; gap: 1rem; }
    .enq-success-icon { width: 4rem; height: 4rem; border-radius: 999px; background: rgba(34,197,94,.12); border: 1.5px solid rgba(34,197,94,.35); display: flex; align-items: center; justify-content: center; }
    .enq-success-icon svg { width: 2rem; height: 2rem; stroke: #22c55e; }
    .enq-success-title { margin: 0; font-size: 1.6rem; font-weight: 800; color: #fff; }
    .enq-success-msg { margin: 0; font-size: .95rem; color: rgba(255,255,255,.6); line-height: 1.6; max-width: 38ch; }

    @media (max-width: 600px) {
        .enq-dialog { padding: 1.75rem 1.25rem; border-radius: 16px; }
        .enq-grid { grid-template-columns: 1fr; }
        .enq-field--full { grid-column: 1; }
        .enq-header { margin-bottom: 1.25rem; }
        .enq-overlay { padding: 0; align-items: flex-end; }
        .enq-dialog { border-radius: 20px 20px 0 0; max-height: 95vh; width: 100%; }
    }
    @media (max-width: 400px) { .enq-dialog { padding: 1.5rem 1rem; } }
</style>

<script>
(() => {
    const overlay    = document.getElementById('enq-overlay');
    const backdrop   = document.getElementById('enq-backdrop');
    const closeBtn   = document.getElementById('enq-close');
    const form       = document.getElementById('enq-form');
    const formWrap   = document.getElementById('enq-form-wrap');
    const success    = document.getElementById('enq-success');
    const submitBtn  = document.getElementById('enq-submit');
    const submitTxt  = document.getElementById('enq-submit-text');
    const submitSpin = document.getElementById('enq-submit-spinner');
    const formErr    = document.getElementById('enq-form-error');
    const pkgSelect  = document.getElementById('enq-package');

    window.openEnquiry = function (packageLabel) {
        if (packageLabel && pkgSelect) {
            for (const opt of pkgSelect.options) {
                opt.selected = opt.value === packageLabel;
            }
        }
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        setTimeout(() => { const first = form.querySelector('input, select, textarea'); if (first) first.focus(); }, 60);
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
        submitBtn.disabled  = on;
        submitTxt.hidden    = on;
        submitSpin.hidden   = !on;
    }

    function clearFieldErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.enq-field-err').forEach(el => el.textContent = '');
    }

    function showFieldError(name, msg) {
        const input = form.querySelector(`[name="${name}"]`);
        const errEl = document.getElementById(`err-${name}`);
        if (input) input.classList.add('is-invalid');
        if (errEl) errEl.textContent = msg;
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
        let hasError = false;
        if (!data.get('name').trim())    { showFieldError('name',    'Please enter your name.');          hasError = true; }
        if (!data.get('email').trim())   { showFieldError('email',   'Please enter your email address.'); hasError = true; }
        if (!data.get('package').trim()) { showFieldError('package', 'Please select a package.');         hasError = true; }
        if (hasError) return;

        setSubmitting(true);

        try {
            const res  = await fetch('{{ route('ticket-packages.enquire') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': data.get('_token'), 'Accept': 'application/json' },
                body: data,
            });
            const json = await res.json();

            if (res.status === 422 && json.errors) {
                Object.entries(json.errors).forEach(([field, msgs]) => showFieldError(field, msgs[0]));
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
