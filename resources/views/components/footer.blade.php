<footer class="wf">
    <div class="wf-inner">

        {{-- Brand column --}}
        <div class="wf-col wf-col-brand">
            <a href="{{ route('home') }}" class="wf-logo-link">
                <img src="{{ asset('images/logos/Wado Ticketing.png') }}" alt="WADO Ticketing" class="wf-logo">
            </a>
            <p class="wf-tagline">Uganda's home for live events.<br>Buy, sell, and manage tickets seamlessly.</p>
            <div class="wf-social">
                <a href="https://www.facebook.com/wadoconcepts" target="_blank" rel="noopener" aria-label="Facebook" class="wf-social-link">
                    <img src="{{ asset('images/facebook-logo.png') }}" alt="Facebook" class="wf-social-icon wf-social-icon--facebook">
                </a>
                <a href="https://www.instagram.com/wadoconcepts" target="_blank" rel="noopener" aria-label="Instagram" class="wf-social-link">
                    <img src="{{ asset('images/logos/insta-logo.png') }}" alt="Instagram" class="wf-social-icon wf-social-icon--instagram">
                </a>
                <a href="https://twitter.com/wadoconcepts" target="_blank" rel="noopener" aria-label="X / Twitter" class="wf-social-link">
                    <img src="{{ asset('images/logos/x-logo.png') }}" alt="X" class="wf-social-icon wf-social-icon--x">
                </a>
                <a href="https://wa.me/256703015846" target="_blank" rel="noopener" aria-label="WhatsApp" class="wf-social-link">
                    <img src="{{ asset('images/logos/whatsap-logo.png') }}" alt="WhatsApp" class="wf-social-icon wf-social-icon--whatsapp">
                </a>
            </div>
        </div>

        {{-- Explore column --}}
        <div class="wf-col">
            <h4 class="wf-col-heading">Explore</h4>
            <ul class="wf-links">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('events.index') }}">Browse Events</a></li>
                <li><a href="{{ route('ticket-packages.index') }}">Ticket Packages</a></li>
                <li><a href="{{ route('contact') }}">Contact Us</a></li>
            </ul>
        </div>

        {{-- Account column --}}
        <div class="wf-col">
            <h4 class="wf-col-heading">Account</h4>
            <ul class="wf-links">
                @auth
                    <li><a href="{{ route('tickets.index') }}">My Tickets</a></li>
                    <li><a href="{{ route('profile.show') }}">Profile</a></li>
                @else
                    <li><a href="{{ route('login') }}">Log In</a></li>
                    <li><a href="{{ route('register') }}">Create Account</a></li>
                @endauth
            </ul>
        </div>

        {{-- Legal column --}}
        <div class="wf-col">
            <h4 class="wf-col-heading">Legal</h4>
            <ul class="wf-links">
                <li><a href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                <li><a href="{{ route('ticket-policy') }}">Ticket Policy</a></li>
                <li><a href="{{ route('refund-policy') }}">Refund &amp; Returns</a></li>
                <li><a href="{{ route('contact') }}">Support</a></li>
            </ul>
        </div>

        {{-- Contact column --}}
        <div class="wf-col">
            <h4 class="wf-col-heading">Contact</h4>
            <ul class="wf-links wf-links-contact">
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                    <span>Kireka, Kampala, Uganda</span>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <a href="mailto:wadoconcepts@gmail.com">wadoconcepts@gmail.com</a>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.85 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.77 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.74a16 16 0 0 0 6.29 6.29l1.1-1.1a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <a href="tel:+256703015846">0703 015 846</a>
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Mon – Fri, 9am – 6pm EAT</span>
                </li>
            </ul>
        </div>

    </div>

    {{-- Mobile compact view --}}
    <div class="wf-mobile">
        
        {{-- Follow us section --}}
        <div class="wf-mobile-section">
            <p class="wf-mobile-title">FOLLOW US</p>
            <div class="wf-mobile-social">
                <a href="https://www.facebook.com/wadoconcepts" target="_blank" rel="noopener" aria-label="Facebook">
                    <img src="{{ asset('images/facebook-logo.png') }}" alt="Facebook" class="wf-mobile-social-icon wf-mobile-social-icon--facebook">
                </a>
                <a href="https://www.instagram.com/wadoconcepts" target="_blank" rel="noopener" aria-label="Instagram">
                    <img src="{{ asset('images/logos/insta-logo.png') }}" alt="Instagram" class="wf-mobile-social-icon wf-mobile-social-icon--instagram">
                </a>
                <a href="https://twitter.com/wadoconcepts" target="_blank" rel="noopener" aria-label="X / Twitter">
                    <img src="{{ asset('images/logos/x-logo.png') }}" alt="X" class="wf-mobile-social-icon wf-mobile-social-icon--x">
                </a>
                <a href="https://wa.me/256703015846" target="_blank" rel="noopener" aria-label="WhatsApp">
                    <img src="{{ asset('images/logos/whatsap-logo.png') }}" alt="WhatsApp" class="wf-mobile-social-icon wf-mobile-social-icon--whatsapp">
                </a>
            </div>
        </div>

        {{-- Two-column section --}}
        <div class="wf-mobile-cols">
            
            {{-- Left: Contact --}}
            <div class="wf-mobile-col">
                <p class="wf-mobile-title">CONTACT US</p>
                <div class="wf-mobile-items">
                    <div class="wf-mobile-item">
                        <p class="wf-mobile-item-label">Address:</p>
                        <p class="wf-mobile-item-val">Kireka, Kampala, Uganda</p>
                    </div>
                    <div class="wf-mobile-item">
                        <p class="wf-mobile-item-label">Contact:</p>
                        <p class="wf-mobile-item-val"><a href="tel:+256703015846">0703 015 846</a></p>
                    </div>
                    <div class="wf-mobile-item">
                        <p class="wf-mobile-item-label">E-mail:</p>
                        <p class="wf-mobile-item-val"><a href="mailto:wadoconcepts@gmail.com">wadoconcepts@gmail.com</a></p>
                    </div>
                </div>
            </div>

            {{-- Right: Navigate/Legal --}}
            <div class="wf-mobile-col">
                <p class="wf-mobile-title">LEGAL &amp; NAV</p>
                <div class="wf-mobile-nav">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('events.index') }}">Events</a>
                    <a href="{{ route('terms') }}">Terms</a>
                    <a href="{{ route('ticket-policy') }}">Ticket Policy</a>
                    <a href="{{ route('refund-policy') }}">Refunds</a>
                    <a href="{{ route('contact') }}">Contact</a>
                </div>
            </div>

        </div>
    </div>

    <div class="wf-bottom">
        <span>&copy; {{ date('Y') }} WADO Ticketing. All rights reserved.</span>
        <span>Developed by <strong>Aloyo Brenda Ojera</strong></span>
    </div>
</footer>

<style>
.wf {
    background: #08111f;
    border-top: 1px solid rgba(255,255,255,.07);
    color: rgba(200,215,235,.75);
    font-size: .9rem;
    padding: 3.5rem 1.25rem 0;
}
.wf-inner {
    max-width: var(--site-width, 1140px);
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1.7fr 1fr 1fr 1fr 1.4fr;
    gap: 2.5rem 2rem;
    padding-bottom: 2.5rem;
    border-bottom: 1px solid rgba(255,255,255,.07);
}
.wf-logo-link { display: inline-block; margin-bottom: .9rem; }
.wf-logo { height: 44px; width: auto; }
.wf-tagline {
    font-size: .86rem;
    line-height: 1.65;
    color: rgba(180,200,230,.6);
    margin-bottom: 1.25rem;
}
.wf-social { display: flex; gap: .55rem; flex-wrap: wrap; }
.wf-social-link {
    width: 34px; height: 34px;
    border-radius: 50%;
    border: 1px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.05);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    color: rgba(200,215,235,.7);
    transition: background .17s, color .17s, border-color .17s;
}
.wf-social-link:hover { background: #2563eb; border-color: #2563eb; color: #fff; }
.wf-social-icon {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    display: block;
}

.wf-social-icon--facebook { transform: scale(1); }
.wf-social-icon--instagram { transform: scale(1.9); }
.wf-social-icon--x { transform: scale(1.2); }
.wf-social-icon--whatsapp { transform: scale(1.08); }

.wf-col-heading {
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: rgba(255,255,255,.9);
    margin: 0 0 1rem;
}
.wf-links {
    list-style: none;
    padding: 0; margin: 0;
    display: flex; flex-direction: column; gap: .55rem;
}
.wf-links a {
    color: rgba(180,200,230,.65);
    text-decoration: none;
    font-size: .88rem;
    transition: color .15s;
}
.wf-links a:hover { color: #fff; }

.wf-links-contact li {
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    font-size: .83rem;
    color: rgba(180,200,230,.6);
    line-height: 1.5;
}
.wf-links-contact svg { width: 14px; height: 14px; flex-shrink: 0; margin-top: .15rem; color: rgba(200,215,235,.5); }
.wf-links-contact a { color: rgba(180,200,230,.65); text-decoration: none; }
.wf-links-contact a:hover { color: #fff; }

.wf-bottom {
    max-width: var(--site-width, 1140px);
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
    padding: 1.25rem 0 1.5rem;
    font-size: .78rem;
    color: rgba(180,200,230,.4);
}
.wf-bottom-links { display: flex; gap: 1.25rem; flex-wrap: wrap; }
.wf-bottom-links a { color: rgba(180,200,230,.4); text-decoration: none; transition: color .15s; }
.wf-bottom-links a:hover { color: rgba(200,220,255,.8); }

/* Mobile compact footer */
.wf-mobile { display: none; }

@media (max-width: 1050px) {
    .wf-inner { grid-template-columns: 1fr 1fr 1fr; }
    .wf-col-brand { grid-column: 1 / -1; }
}
@media (max-width: 640px) {
    .wf { padding: 1.5rem 1rem 0; }
    .wf-inner { display: none; }
    .wf-mobile { display: block; }

    .wf-mobile {
        padding: 0 1rem;
    }

    .wf-mobile-section {
        text-align: center;
        padding-bottom: 1.25rem;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }

    .wf-mobile-title {
        font-size: .65rem;
        letter-spacing: .15em;
        text-transform: uppercase;
        color: rgba(255,255,255,.75);
        font-weight: 700;
        margin: 0 0 1rem;
    }

    .wf-mobile-social {
        display: flex;
        gap: .8rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    .wf-mobile-social a {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 1.2px solid rgba(255,255,255,.15);
        background: rgba(255,255,255,.08);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: rgba(200,215,235,.6);
        transition: background .15s, color .15s, border-color .15s;
        text-decoration: none;
    }
    .wf-mobile-social a:hover { background: #c0283c; color: #fff; border-color: #c0283c; }
    .wf-mobile-social-icon {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        display: block;
    }

    .wf-mobile-social-icon--facebook { transform: scale(1); }
    .wf-mobile-social-icon--instagram { transform: scale(1.9); }
    .wf-mobile-social-icon--x { transform: scale(1.2); }
    .wf-mobile-social-icon--whatsapp { transform: scale(1.08); }

    .wf-mobile-cols {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 1.25rem;
        margin: 0 auto 0.75rem;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }

    .wf-mobile-col {
        display: flex;
        flex-direction: column;
    }

    .wf-mobile-col:last-child {
        align-items: flex-end;
        text-align: right;
    }

    .wf-mobile-items { display: flex; flex-direction: column; gap: .8rem; }

    .wf-mobile-item { }
    .wf-mobile-item-label {
        font-size: .65rem;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: rgba(255,255,255,.65);
        font-weight: 700;
        margin: 0 0 .25rem;
    }
    .wf-mobile-item-val {
        font-size: .82rem;
        color: rgba(180,200,230,.75);
        margin: 0;
        line-height: 1.5;
    }
    .wf-mobile-item-val a {
        color: rgba(180,200,230,.8);
        text-decoration: none;
        transition: color .15s;
    }
    .wf-mobile-item-val a:hover { color: #fff; }

    .wf-mobile-nav {
        display: flex;
        flex-direction: column;
        gap: .5rem;
    }
    .wf-mobile-nav a {
        color: rgba(180,200,230,.75);
        text-decoration: none;
        font-size: .82rem;
        transition: color .15s;
    }
    .wf-mobile-nav a:hover { color: #fff; }

    .wf-bottom {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: .3rem;
        padding: .9rem 0 1.25rem;
    }
}
</style>
