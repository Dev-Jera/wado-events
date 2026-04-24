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
                        <a href="mailto:wadoconcepts@gmail.com?subject=Enquiry: {{ urlencode($pkg['label']) }}"
                           class="tp-card-cta">Get in touch</a>
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
                <a href="mailto:wadoconcepts@gmail.com" class="tp-cta-btn">Contact us</a>
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

@endsection
