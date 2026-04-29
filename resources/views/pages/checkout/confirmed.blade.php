@extends('layouts.app')

@section('content')
<style>
    .conf-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6rem 1rem 4rem;
        background: #150508;
    }

    .conf-card {
        width: min(520px, 100%);
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.10);
        border-radius: 24px;
        padding: 2.5rem 2rem;
        text-align: center;
        box-shadow: 0 24px 56px rgba(0,0,0,.35);
    }

    .conf-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(34,197,94,.15);
        border: 2px solid rgba(34,197,94,.35);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        animation: conf-pop .45s cubic-bezier(.34,1.56,.64,1) both;
    }

    .conf-icon svg {
        width: 36px;
        height: 36px;
        stroke: #22c55e;
    }

    @keyframes conf-pop {
        from { opacity: 0; transform: scale(.5); }
        to   { opacity: 1; transform: scale(1); }
    }

    .conf-title {
        font-size: 1.55rem;
        font-weight: 800;
        color: #fff;
        margin: 0 0 .5rem;
        letter-spacing: -.02em;
    }

    .conf-event {
        font-size: .9rem;
        font-weight: 600;
        color: rgba(255,180,185,.75);
        margin: 0 0 1.75rem;
    }

    .conf-email-box {
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        margin-bottom: 1.5rem;
        text-align: left;
    }

    .conf-email-label {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: rgba(255,215,220,.5);
        margin-bottom: .3rem;
    }

    .conf-email-addr {
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
        word-break: break-all;
    }

    .conf-steps {
        list-style: none;
        padding: 0;
        margin: 0 0 2rem;
        display: grid;
        gap: .65rem;
        text-align: left;
    }

    .conf-step {
        display: flex;
        align-items: flex-start;
        gap: .7rem;
        font-size: .875rem;
        color: rgba(255,215,220,.75);
        line-height: 1.5;
    }

    .conf-step-num {
        flex-shrink: 0;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: rgba(192,40,60,.25);
        border: 1px solid rgba(192,40,60,.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .72rem;
        font-weight: 700;
        color: #f08090;
        margin-top: .1rem;
    }

    .conf-code {
        font-family: monospace;
        font-size: .75rem;
        color: rgba(255,255,255,.4);
        letter-spacing: .08em;
        margin-bottom: 2rem;
    }

    .conf-actions {
        display: flex;
        flex-direction: column;
        gap: .6rem;
    }

    .conf-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        height: 48px;
        border-radius: 999px;
        font-size: .92rem;
        font-weight: 700;
        font-family: inherit;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: background .18s, box-shadow .18s;
    }

    .conf-btn-primary {
        background: #c0283c;
        color: #fff;
        box-shadow: 0 4px 18px rgba(192,40,60,.35);
    }

    .conf-btn-primary:hover {
        background: #a01e2e;
        box-shadow: 0 6px 22px rgba(192,40,60,.48);
    }

    .conf-btn-ghost {
        background: rgba(255,255,255,.06);
        color: rgba(255,215,220,.8);
        border: 1px solid rgba(255,255,255,.12);
    }

    .conf-btn-ghost:hover {
        background: rgba(255,255,255,.10);
        color: #fff;
    }

    @media (max-width: 480px) {
        .conf-card { padding: 2rem 1.25rem; border-radius: 18px; }
        .conf-title { font-size: 1.3rem; }
    }
</style>

<div class="conf-page">
    <div class="conf-card">

        <div class="conf-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        </div>

        <h1 class="conf-title">You're in! 🎉</h1>
        <p class="conf-event">{{ $event_title }}</p>

        @if ($email)
        <div class="conf-email-box">
            <p class="conf-email-label">Ticket sent to</p>
            <p class="conf-email-addr">{{ $email }}</p>
        </div>
        @endif

        <ol class="conf-steps">
            <li class="conf-step">
                <span class="conf-step-num">1</span>
                <span>Check your inbox — your ticket and QR code have been sent to your email.</span>
            </li>
            <li class="conf-step">
                <span class="conf-step-num">2</span>
                <span>Check your spam or junk folder if it doesn't arrive within a few minutes.</span>
            </li>
            <li class="conf-step">
                <span class="conf-step-num">3</span>
                <span>Show the QR code at the gate on event day to get in.</span>
            </li>
        </ol>

        @if ($ticket_code)
        <p class="conf-code">TICKET CODE: {{ $ticket_code }}</p>
        @endif

        <div class="conf-actions">
            <a href="{{ route('events.index') }}" class="conf-btn conf-btn-primary">
                Browse more events
            </a>
            <a href="{{ url('/') }}" class="conf-btn conf-btn-ghost">
                Back to home
            </a>
        </div>

    </div>
</div>
@endsection
