<footer class="wf" role="contentinfo">
    <div class="wf-inner">
        <section class="wf-col wf-col-brand" aria-label="Brand">
            <a href="{{ route('home') }}" class="wf-logo-link">
                <img src="{{ asset('images/logos/Wado Ticketing.png') }}" alt="WADO Ticketing" class="wf-logo">
            </a>
            <p class="wf-tagline">Uganda's home for live events. Buy, sell, and manage tickets seamlessly.</p>
            <h4 class="wf-col-heading wf-follow-heading">Follow Us</h4>
            <div class="wf-social" aria-label="Social links">
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
        </section>

        <section class="wf-mobile-grid" aria-label="Footer mobile links">
            <nav class="wf-mobile-col" aria-label="Contact us">
                <h4 class="wf-col-heading">Contact Us</h4>
                <ul class="wf-mobile-list">
                    <li>
                        <span class="wf-mobile-k">Address:</span>
                        <span>Kireka, Kampala, Uganda</span>
                    </li>
                    <li>
                        <span class="wf-mobile-k">Contact:</span>
                        <a href="tel:+256703015846">0703 015 846</a>
                    </li>
                    <li>
                        <span class="wf-mobile-k">E-mail:</span>
                        <a href="mailto:wadoconcepts@gmail.com">wadoconcepts@gmail.com</a>
                    </li>
                </ul>
            </nav>

            <nav class="wf-mobile-col" aria-label="Legal and navigation">
                <h4 class="wf-col-heading">Legal &amp; Nav</h4>
                <ul class="wf-links wf-mobile-nav">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('events.index') }}">Events</a></li>
                    <li><a href="{{ route('terms') }}">Terms</a></li>
                    <li><a href="{{ route('ticket-policy') }}">Ticket Policy</a></li>
                    <li><a href="{{ route('refund-policy') }}">Refunds</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </nav>
        </section>

        <nav class="wf-col" aria-label="Explore">
            <h4 class="wf-col-heading">Explore</h4>
            <ul class="wf-links">
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('events.index') }}">Browse Events</a></li>
                <li><a href="{{ route('ticket-packages.index') }}">Ticket Packages</a></li>
                <li><a href="{{ route('contact') }}">Contact Us</a></li>
            </ul>
        </nav>

        <nav class="wf-col" aria-label="Account">
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
        </nav>

        <nav class="wf-col" aria-label="Legal">
            <h4 class="wf-col-heading">Legal</h4>
            <ul class="wf-links">
                <li><a href="{{ route('terms') }}">Terms &amp; Conditions</a></li>
                <li><a href="{{ route('ticket-policy') }}">Ticket Policy</a></li>
                <li><a href="{{ route('refund-policy') }}">Refund &amp; Returns</a></li>
                <li><a href="{{ route('contact') }}">Support</a></li>
            </ul>
        </nav>

        <section class="wf-col" aria-label="Contact">
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
                    <span>Mon - Fri, 9am - 6pm EAT</span>
                </li>
            </ul>
        </section>
    </div>

    <div class="wf-bottom">
        <span>&copy; {{ date('Y') }} WADO Ticketing. All rights reserved.</span>
        <span class="wf-credit">Developed by <strong>Aloyo Brenda Ojera</strong></span>
    </div>
</footer>

<style>
.wf {
    background: #171013;
    border-top: 1px solid rgba(255,255,255,.08);
    color: rgba(247,244,245,.76);
    font-size: .9rem;
    padding: 3rem 1.25rem 0;
}

.wf-inner {
    max-width: var(--site-width, 1140px);
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1fr 1.35fr;
    gap: 2rem 1.5rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid rgba(255,255,255,.09);
}

.wf-logo-link {
    display: inline-block;
    margin-bottom: .9rem;
}

.wf-logo {
    height: 44px;
    width: auto;
}

.wf-tagline {
    font-size: .88rem;
    line-height: 1.62;
    color: rgba(247,244,245,.62);
    margin: 0 0 1.15rem;
    max-width: 34ch;
}

.wf-follow-heading {
    display: none;
    margin: 0 0 .7rem;
}

.wf-social {
    display: flex;
    gap: .55rem;
    flex-wrap: wrap;
}

.wf-social-link {
    width: 34px;
    height: 34px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.04);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    transition: border-color .16s, background .16s;
}

.wf-social-link:hover {
    border-color: rgba(192,40,60,.65);
    background: rgba(192,40,60,.18);
}

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

.wf-mobile-grid {
    display: none;
}

.wf-mobile-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: .66rem;
}

.wf-mobile-list li {
    display: flex;
    flex-direction: column;
    gap: .2rem;
    font-size: .86rem;
    color: rgba(247,244,245,.74);
    line-height: 1.35;
}

.wf-mobile-k {
    text-transform: uppercase;
    letter-spacing: .06em;
    font-size: .72rem;
    font-weight: 700;
    color: rgba(247,244,245,.88);
}

.wf-mobile-list a {
    color: rgba(247,244,245,.74);
    text-decoration: none;
}

.wf-mobile-list a:hover {
    color: #fff;
}

.wf-mobile-nav {
    gap: .48rem;
}

.wf-col-heading {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: rgba(247,244,245,.9);
    margin: 0 0 .92rem;
}

.wf-links {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: .5rem;
}

.wf-links a {
    color: rgba(247,244,245,.68);
    text-decoration: none;
    font-size: .88rem;
    transition: color .15s;
}

.wf-links a:hover {
    color: #fff;
}

.wf-links-contact li {
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    color: rgba(247,244,245,.64);
    line-height: 1.5;
    font-size: .84rem;
}

.wf-links-contact svg {
    width: 14px;
    height: 14px;
    margin-top: .15rem;
    flex-shrink: 0;
    color: rgba(247,244,245,.46);
}

.wf-bottom {
    max-width: var(--site-width, 1140px);
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .9rem;
    flex-wrap: wrap;
    padding: 1.15rem 0 1.35rem;
    color: rgba(247,244,245,.5);
    font-size: .78rem;
}

.wf-credit strong {
    color: rgba(247,244,245,.72);
    font-weight: 600;
}

@media (max-width: 1080px) {
    .wf-inner {
        grid-template-columns: 1fr 1fr 1fr;
    }

    .wf-col-brand {
        grid-column: 1 / -1;
    }
}

@media (max-width: 760px) {
    .wf {
        padding: 2rem 1rem 0;
    }

    .wf-inner {
        grid-template-columns: 1fr;
        gap: 1.1rem;
        padding-bottom: 1.35rem;
    }

    .wf-col-brand {
        grid-column: 1 / -1;
    }

    .wf-logo-link,
    .wf-tagline,
    .wf-inner > .wf-col:not(.wf-col-brand) {
        display: none;
    }

    .wf-col-brand {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(255,255,255,.09);
    }

    .wf-follow-heading {
        display: block;
        margin-bottom: .75rem;
    }

    .wf-social {
        justify-content: center;
    }

    .wf-mobile-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.2rem 1.1rem;
        padding-bottom: .2rem;
        align-items: start;
    }

    .wf-mobile-grid .wf-mobile-col:first-child {
        justify-self: start;
        text-align: left;
    }

    .wf-mobile-grid .wf-mobile-col:last-child {
        justify-self: end;
        text-align: left;
    }

    .wf-bottom {
        padding: .95rem 0 1.1rem;
        font-size: .75rem;
        justify-content: center;
        text-align: center;
    }
}

@media (max-width: 520px) {
    .wf-mobile-grid {
        gap: 1rem .9rem;
    }

    .wf-bottom {
        flex-direction: column;
        align-items: center;
        gap: .35rem;
    }
}
</style>
