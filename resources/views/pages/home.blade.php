@extends('layouts.app')

@section('content')
    <section class="home-hero">
        <div class="hero-shell">
            <div class="hero-surface">
                <div class="hero-slides" aria-hidden="true">
                    <div class="hero-slide is-active" style="background-image: url('{{ asset('images/home-hero-bg.jpg') }}');"></div>
                    <div class="hero-slide" style="background-image: url('{{ asset('images/hero-image2.jpg') }}');"></div>
                    <div class="hero-slide" style="background-image: url('{{ asset('images/hero-image3.jfif') }}');"></div>
                </div>

                <div class="hero-copy">
                    <p class="hero-brand">HometownTickets</p>
                    <h1 class="hero-title">Ticketing Made Simple</h1>
                    <p class="hero-kicker">Experience events effortlessly, from booking to the big day.</p>
                    <p class="hero-description">
                        
                        <span class="hero-description-line">Sell tickets, manage attendees, and let your audience enjoy a seamless experience.</span>
                    </p>
                    <a class="hero-cta" href="/events">Book Tickets Now</a>
                </div>
            </div>
        </div>
    </section>

    <section class="categories-marquee" aria-label="Event categories">
        <div class="categories-shell">
            <svg class="icon-sprite" aria-hidden="true" focusable="false">
                <symbol id="icon-music" viewBox="0 0 24 24">
                    <path d="M9 18a2.5 2.5 0 1 1-5 0a2.5 2.5 0 0 1 5 0zm11-3a2.5 2.5 0 1 1-5 0a2.5 2.5 0 0 1 5 0zM9 18V6l11-2v11" />
                </symbol>
                <symbol id="icon-sports" viewBox="0 0 24 24">
                    <path d="M12 3a9 9 0 1 0 9 9a9 9 0 0 0-9-9zm-5.7 9a11 11 0 0 1 2-5.6M17.7 12a11 11 0 0 1-2 5.6M8.9 6.4l2.2 2.2m1.8 0l2.2-2.2m-4 6.8l-2.2 2.2m3.1 1.3h0m1.8-3.5l2.2 2.2" />
                </symbol>
                <symbol id="icon-theater" viewBox="0 0 24 24">
                    <path d="M4 5l4 2l4-2l4 2l4-2v11a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4zm5 7h.01M15 7h.01M9 14c1 .8 2 1.2 3 1.2s2-.4 3-1.2" />
                </symbol>
                <symbol id="icon-briefcase" viewBox="0 0 24 24">
                    <path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m-11 3h16v8a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3zm0 0v-8m16 8v-8M10 12h4" />
                </symbol>
                <symbol id="icon-tools" viewBox="0 0 24 24">
                    <path d="M14.5 6.5a4 4 0 0 0-5.6 5.6L3 18v3h3l5.9-5.9a4 4 0 0 0 5.6-5.6l-2 2h-2v-2z" />
                </symbol>
                <symbol id="icon-community" viewBox="0 0 24 24">
                    <path d="M16 11a3 3 0 1 0-3-3a3 3 0 0 0 3 3zM8 12a3 3 0 1 0-3-3a3 3 0 0 0 3 3zm8 1c-2.8 0-5 1.2-5 3v2h10v-2c0-1.8-2.2-3-5-3zM8 14c-2.8 0-5 1.2-5 3v1h7v-1c0-1 .4-1.9 1.1-2.6A7.6 7.6 0 0 0 8 14z" />
                </symbol>
                <symbol id="icon-comedy" viewBox="0 0 24 24">
                    <path d="M12 3a9 9 0 1 0 9 9a9 9 0 0 0-9-9zm-3 7h.01M15 10h.01M8 14c1.2 1.3 2.5 2 4 2s2.8-.7 4-2" />
                </symbol>
                <symbol id="icon-film" viewBox="0 0 24 24">
                    <path d="M3 6h18v12H3zm4 0v12M17 6v12M3 10h4m-4 4h4m10-4h4m-4 4h4" />
                </symbol>
                <symbol id="icon-free" viewBox="0 0 24 24">
                    <path d="M12 2l2.3 4.7 5.2.8-3.7 3.6.9 5.1L12 14l-4.7 2.2.9-5.1L4.5 7.5l5.2-.8L12 2z" />
                </symbol>
            </svg>

            <div class="categories-track-wrap">
                <div class="categories-track" role="list">
                    <span class="category-pill is-active" role="button" tabindex="0" data-category="all" aria-pressed="true">
                        <span class="category-pill-top"><svg class="cat-icon" aria-hidden="true"><use href="#icon-community"></use></svg><span>All</span></span>
                        <span class="category-pill-sub">9 events</span>
                    </span>
                    <span class="category-pill" role="button" tabindex="0" data-category="music" aria-pressed="false">
                        <span class="category-pill-top"><svg class="cat-icon" aria-hidden="true"><use href="#icon-music"></use></svg><span>Music</span></span>
                        <span class="category-pill-sub">8 events</span>
                    </span>
                    <span class="category-pill" role="button" tabindex="0" data-category="sports" aria-pressed="false">
                        <span class="category-pill-top"><svg class="cat-icon" aria-hidden="true"><use href="#icon-sports"></use></svg><span>Sports</span></span>
                        <span class="category-pill-sub">6 events</span>
                    </span>
                    <span class="category-pill" role="button" tabindex="0" data-category="theater" aria-pressed="false">
                        <span class="category-pill-top"><svg class="cat-icon" aria-hidden="true"><use href="#icon-theater"></use></svg><span>Theater</span></span>
                        <span class="category-pill-sub">5 events</span>
                    </span>
                    <span class="category-pill" role="button" tabindex="0" data-category="conference" aria-pressed="false">
                        <span class="category-pill-top"><svg class="cat-icon" aria-hidden="true"><use href="#icon-briefcase"></use></svg><span>Conference</span></span>
                        <span class="category-pill-sub">4 events</span>
                    </span>
                    <span class="category-pill" role="button" tabindex="0" data-category="workshop" aria-pressed="false">
                        <span class="category-pill-top"><svg class="cat-icon" aria-hidden="true"><use href="#icon-tools"></use></svg><span>Workshop</span></span>
                        <span class="category-pill-sub">4 events</span>
                    </span>
                    <span class="category-pill" role="button" tabindex="0" data-category="community" aria-pressed="false">
                        <span class="category-pill-top"><svg class="cat-icon" aria-hidden="true"><use href="#icon-community"></use></svg><span>Community</span></span>
                        <span class="category-pill-sub">4 events</span>
                    </span>
                    <span class="category-pill" role="button" tabindex="0" data-category="comedy" aria-pressed="false">
                        <span class="category-pill-top"><svg class="cat-icon" aria-hidden="true"><use href="#icon-comedy"></use></svg><span>Comedy</span></span>
                        <span class="category-pill-sub">4 events</span>
                    </span>
                    <span class="category-pill" role="button" tabindex="0" data-category="film" aria-pressed="false">
                        <span class="category-pill-top"><svg class="cat-icon" aria-hidden="true"><use href="#icon-film"></use></svg><span>Film</span></span>
                        <span class="category-pill-sub">5 events</span>
                    </span>
                    <span class="category-pill" role="button" tabindex="0" data-category="free" aria-pressed="false">
                        <span class="category-pill-top"><svg class="cat-icon" aria-hidden="true"><use href="#icon-free"></use></svg><span>Free Events</span></span>
                        <span class="category-pill-sub">3 events</span>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="trending-events" aria-label="Trending events">
        <div class="trending-shell">
            <div class="trending-head">
                <p class="trending-kicker">DISCOVER WHAT IS HOT</p>
                <h2 class="trending-title">Trending Events</h2>
            </div>

            <div class="trending-grid">
                <article class="event-card">
                    <div class="event-thumb" style="background-image: url('{{ asset('images/music.jpg') }}');"></div>
                    <div class="event-content">
                        <h3>Live Music Night</h3>
                        <p>Concerts & Music Shows</p>
                    </div>
                </article>

                <article class="event-card event-card-featured">
                    <div class="event-thumb" style="background-image: url('{{ asset('images/conference.jpg') }}');"></div>
                    <div class="event-content">
                        <h3>Business Connect 2026</h3>
                        <p>Conferences & Corporate Events</p>
                    </div>
                </article>

                <article class="event-card">
                    <div class="event-thumb" style="background-image: url('{{ asset('images/movie.jpg') }}');"></div>
                    <div class="event-content">
                        <h3>Film Premiere Weekend</h3>
                        <p>Film & Screenings</p>
                    </div>
                </article>

                <article class="event-card">
                    <div class="event-thumb" style="background-image: url('{{ asset('images/socker.jpg') }}');"></div>
                    <div class="event-content">
                        <h3>City Sports Clash</h3>
                        <p>Sports & Games</p>
                    </div>
                </article>

                <article class="event-card">
                    <div class="event-thumb" style="background-image: url('{{ asset('images/skilling.jpg') }}');"></div>
                    <div class="event-content">
                        <h3>Skills Bootcamp</h3>
                        <p>Workshops & Training</p>
                    </div>
                </article>
            </div>
        </div>
    </section>
<section class="upcoming-events" aria-label="Upcoming events">
    <div class="upcoming-shell">

        <div class="upcoming-head">
            <div class="upcoming-head-text">
                <p class="upcoming-kicker">DON'T MISS OUT</p>
                <h2 class="upcoming-title">Upcoming Events</h2>
            </div>
            <a href="/events" class="upcoming-view-all">View All Events</a>
        </div>

        <div class="upcoming-grid" id="upcoming-grid">

            <article class="ucard" data-category="music">
                <div class="ucard-thumb" style="background-image: url('{{ asset('images/music.jpg') }}');"></div>
                <div class="ucard-date-badge"><span class="ucard-day">14</span><span class="ucard-mon">JUN</span></div>
                <div class="ucard-body">
                    <span class="ucard-cat">Music</span>
                    <h3 class="ucard-title">Live Music Night</h3>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                        Kampala, Uganda
                    </p>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        7:00 PM
                    </p>
                    <div class="ucard-footer">
                        <span class="ucard-price">UGX 50,000</span>
                        <a href="/events/1" class="ucard-btn">Get Tickets</a>
                    </div>
                </div>
            </article>

            <article class="ucard" data-category="conference">
                <div class="ucard-thumb" style="background-image: url('{{ asset('images/conference.jpg') }}');"></div>
                <div class="ucard-date-badge"><span class="ucard-day">21</span><span class="ucard-mon">JUN</span></div>
                <div class="ucard-body">
                    <span class="ucard-cat">Conference</span>
                    <h3 class="ucard-title">Business Connect 2026</h3>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                        Serena Hotel, Kampala
                    </p>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        9:00 AM
                    </p>
                    <div class="ucard-footer">
                        <span class="ucard-price">UGX 150,000</span>
                        <a href="/events/2" class="ucard-btn">Get Tickets</a>
                    </div>
                </div>
            </article>

            <article class="ucard" data-category="film">
                <div class="ucard-thumb" style="background-image: url('{{ asset('images/movie.jpg') }}');"></div>
                <div class="ucard-date-badge"><span class="ucard-day">28</span><span class="ucard-mon">JUN</span></div>
                <div class="ucard-body">
                    <span class="ucard-cat">Film</span>
                    <h3 class="ucard-title">Film Premiere Weekend</h3>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                        Century Cinemax, Kampala
                    </p>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        6:30 PM
                    </p>
                    <div class="ucard-footer">
                        <span class="ucard-price">UGX 30,000</span>
                        <a href="/events/3" class="ucard-btn">Get Tickets</a>
                    </div>
                </div>
            </article>

            <article class="ucard" data-category="sports">
                <div class="ucard-thumb" style="background-image: url('{{ asset('images/socker.jpg') }}');"></div>
                <div class="ucard-date-badge"><span class="ucard-day">05</span><span class="ucard-mon">JUL</span></div>
                <div class="ucard-body">
                    <span class="ucard-cat">Sports</span>
                    <h3 class="ucard-title">City Sports Clash</h3>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                        Mandela National Stadium
                    </p>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        3:00 PM
                    </p>
                    <div class="ucard-footer">
                        <span class="ucard-price">UGX 20,000</span>
                        <a href="/events/4" class="ucard-btn">Get Tickets</a>
                    </div>
                </div>
            </article>

            <article class="ucard" data-category="workshop">
                <div class="ucard-thumb" style="background-image: url('{{ asset('images/skilling.jpg') }}');"></div>
                <div class="ucard-date-badge"><span class="ucard-day">12</span><span class="ucard-mon">JUL</span></div>
                <div class="ucard-body">
                    <span class="ucard-cat">Workshop</span>
                    <h3 class="ucard-title">Skills Bootcamp</h3>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                        Innovation Village, Kampala
                    </p>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        10:00 AM
                    </p>
                    <div class="ucard-footer">
                        <span class="ucard-price">UGX 75,000</span>
                        <a href="/events/5" class="ucard-btn">Get Tickets</a>
                    </div>
                </div>
            </article>

            <article class="ucard" data-category="comedy">
                <div class="ucard-thumb" style="background-image: url('{{ asset('images/home-hero-bg.jpg') }}');"></div>
                <div class="ucard-date-badge"><span class="ucard-day">19</span><span class="ucard-mon">JUL</span></div>
                <div class="ucard-body">
                    <span class="ucard-cat">Comedy</span>
                    <h3 class="ucard-title">Laugh Factory Kampala</h3>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                        Kyadondo Rugby Club
                    </p>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        8:00 PM
                    </p>
                    <div class="ucard-footer">
                        <span class="ucard-price">UGX 40,000</span>
                        <a href="/events/6" class="ucard-btn">Get Tickets</a>
                    </div>
                </div>
            </article>

            <article class="ucard" data-category="theater">
                <div class="ucard-thumb" style="background-image: url('{{ asset('images/hero-image2.jpg') }}');"></div>
                <div class="ucard-date-badge"><span class="ucard-day">26</span><span class="ucard-mon">JUL</span></div>
                <div class="ucard-body">
                    <span class="ucard-cat">Theater</span>
                    <h3 class="ucard-title">Midnight Stage Play</h3>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                        National Theatre, Kampala
                    </p>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        7:30 PM
                    </p>
                    <div class="ucard-footer">
                        <span class="ucard-price">UGX 35,000</span>
                        <a href="/events/7" class="ucard-btn">Get Tickets</a>
                    </div>
                </div>
            </article>

            <article class="ucard" data-category="community">
                <div class="ucard-thumb" style="background-image: url('{{ asset('images/hero-image3.jfif') }}');"></div>
                <div class="ucard-date-badge"><span class="ucard-day">02</span><span class="ucard-mon">AUG</span></div>
                <div class="ucard-body">
                    <span class="ucard-cat">Community</span>
                    <h3 class="ucard-title">Neighbourhood Fest</h3>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                        Lugogo, Kampala
                    </p>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        12:00 PM
                    </p>
                    <div class="ucard-footer">
                        <span class="ucard-price is-free">Free</span>
                        <a href="/events/8" class="ucard-btn">Get Tickets</a>
                    </div>
                </div>
            </article>

            <article class="ucard" data-category="community">
                <div class="ucard-thumb" style="background-image: url('{{ asset('images/hero-image2.jpg') }}');"></div>
                <div class="ucard-date-badge"><span class="ucard-day">09</span><span class="ucard-mon">AUG</span></div>
                <div class="ucard-body">
                    <span class="ucard-cat">Free Event</span>
                    <h3 class="ucard-title">Open Air Praise Night</h3>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg>
                        Freedom Grounds, Kampala
                    </p>
                    <p class="ucard-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        5:30 PM
                    </p>
                    <div class="ucard-footer">
                        <span class="ucard-price is-free">Free</span>
                        <a href="/events/9" class="ucard-btn">Get Tickets</a>
                    </div>
                </div>
            </article>

        </div>

        <div class="upcoming-footer">
            <a href="/events" class="upcoming-all-btn">Browse All Events</a>
        </div>

    </div>
</section>

<div class="event-modal" id="event-modal" aria-hidden="true">
    <div class="event-modal-backdrop" data-modal-close></div>
    <article class="event-modal-card" role="dialog" aria-modal="true" aria-labelledby="event-modal-title">
        <button class="event-modal-close" type="button" data-modal-close aria-label="Close event details">×</button>
        <div class="event-modal-image" id="event-modal-image"></div>
        <div class="event-modal-content">
            <p class="event-modal-category" id="event-modal-category">Category</p>
            <h3 class="event-modal-title" id="event-modal-title">Event Title</h3>
            <p class="event-modal-description" id="event-modal-description">Event details and experience information will appear here.</p>

            <div class="event-modal-meta-grid">
                <p class="event-modal-row"><strong>Date:</strong> <span id="event-modal-date">Upcoming</span></p>
                <p class="event-modal-row"><strong>Time:</strong> <span id="event-modal-time">TBA</span></p>
                <p class="event-modal-row"><strong>Location:</strong> <span id="event-modal-location">Venue details available</span></p>
                <p class="event-modal-row"><strong>Price:</strong> <span id="event-modal-price">Check listing</span></p>
            </div>

            <div class="event-modal-section">
                <h4>Artists</h4>
                <div class="event-modal-artists" id="event-modal-artists"></div>
            </div>

            <div class="event-modal-section">
                <h4>Attending</h4>
                <div class="event-modal-attendees" id="event-modal-attendees"></div>
            </div>

            <div class="event-modal-actions">
                <button class="event-modal-buy" id="event-modal-buy" type="button">Buy Ticket</button>
                <a class="event-modal-link" id="event-modal-link" href="/events">View Event</a>
            </div>

            <section class="event-payment-options" id="event-payment-options" hidden>
                <p class="event-payment-title">Choose Payment Method</p>
                <div class="event-payment-qty-wrap">
                    <span class="event-payment-qty-label">Tickets</span>
                    <div class="event-payment-qty">
                        <button type="button" class="qty-btn" id="ticket-qty-minus" aria-label="Decrease ticket quantity">-</button>
                        <span class="qty-value" id="ticket-qty-value">1</span>
                        <button type="button" class="qty-btn" id="ticket-qty-plus" aria-label="Increase ticket quantity">+</button>
                    </div>
                </div>
                <p class="event-payment-amount">Total: <strong id="event-payment-amount">UGX 0</strong></p>
                <div class="event-payment-grid">
                    <button type="button" class="payment-option">Mobile Money</button>
                    <button type="button" class="payment-option">Card (Visa/Mastercard)</button>
                    <button type="button" class="payment-option">Bank Transfer</button>
                </div>
            </section>
        </div>
    </article>
</div>

<style>
    .upcoming-events {
        background: #f4f6f9;
        padding: 3rem 1rem 3.5rem;
    }

    .upcoming-shell {
        width: min(1180px, 100%);
        margin: 0 auto;
    }

    .upcoming-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 1.4rem;
        gap: 1rem;
    }

    .upcoming-kicker {
        margin: 0;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.13em;
        color: #b91c1c;
    }

    .upcoming-title {
        margin: 0.3rem 0 0;
        font-size: clamp(1.4rem, 2.4vw, 2rem);
        color: #18212b;
        line-height: 1.1;
    }

    .upcoming-view-all {
        font-size: 0.85rem;
        font-weight: 600;
        color: #b91c1c;
        text-decoration: none;
        white-space: nowrap;
        border-bottom: 1px solid currentColor;
        padding-bottom: 1px;
    }

    .upcoming-view-all:hover {
        opacity: 0.75;
    }

    /* grid */
    .upcoming-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.1rem;
    }

    /* card */
    .ucard {
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.07);
        box-shadow: 0 4px 16px rgba(20,30,48,0.08);
        position: relative;
        transition: transform 0.22s ease, box-shadow 0.22s ease;
        display: flex;
        flex-direction: column;
        cursor: pointer;
    }

    .ucard:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(20,30,48,0.14);
    }

    .ucard-thumb {
        width: 100%;
        aspect-ratio: 3 / 2;
        background-size: cover;
        background-position: center;
    }

    .ucard-date-badge {
        position: absolute;
        top: 0.7rem;
        right: 0.7rem;
        background: #fff;
        border-radius: 8px;
        padding: 0.3rem 0.55rem;
        text-align: center;
        line-height: 1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1px;
    }

    .ucard-day {
        font-size: 1rem;
        font-weight: 800;
        color: #b91c1c;
    }

    .ucard-mon {
        font-size: 0.6rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: #6b7280;
    }

    .ucard-body {
        padding: 0.9rem 1rem 1rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .ucard-cat {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        color: #b91c1c;
        text-transform: uppercase;
        margin-bottom: 0.3rem;
    }

    .ucard-title {
        margin: 0 0 0.6rem;
        font-size: 0.97rem;
        font-weight: 700;
        color: #18212b;
        line-height: 1.25;
    }

    .ucard-meta {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin: 0 0 0.25rem;
        font-size: 0.78rem;
        color: #5b6979;
        line-height: 1.3;
    }

    .ucard-meta svg {
        width: 0.85rem;
        height: 0.85rem;
        flex-shrink: 0;
        opacity: 0.7;
    }

    .ucard-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
        padding-top: 0.8rem;
        border-top: 1px solid #f0f2f5;
    }

    .ucard-price {
        font-size: 0.9rem;
        font-weight: 700;
        color: #18212b;
    }

    .ucard-price.is-free {
        color: #0f9f5b;
    }

    .ucard-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.4rem 0.9rem;
        border-radius: 999px;
        background: linear-gradient(90deg, #ef4444, #b91c1c);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 600;
        text-decoration: none;
        transition: filter 0.18s ease;
    }

    .ucard-btn:hover {
        filter: brightness(1.1);
    }

    /* hidden state for filter */
    .ucard.is-hidden {
        display: none;
    }


    /* footer cta */
    .upcoming-footer {
        text-align: center;
        margin-top: 2.2rem;
    }

    .upcoming-all-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.7rem 2rem;
        border-radius: 999px;
        border: 2px solid #b91c1c;
        color: #b91c1c;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .upcoming-all-btn:hover {
        background: #b91c1c;
        color: #fff;
    }

    .event-modal {
        position: fixed;
        inset: 0;
        z-index: 120;
        display: grid;
        align-items: center;
        justify-items: center;
        padding: 1rem;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.28s ease, visibility 0.28s ease;
    }

    .event-modal.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .event-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(3, 8, 18, 0.78);
        backdrop-filter: blur(6px);
        opacity: 0;
        transition: opacity 0.28s ease;
    }

    .event-modal.is-open .event-modal-backdrop {
        opacity: 1;
    }

    .event-modal-card {
        position: relative;
        width: min(980px, calc(100% - 1rem));
        max-height: 90vh;
        height: auto;
        overflow: hidden;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 30px 70px rgba(6, 14, 28, 0.45);
        border: 1px solid #c8d5e6;
        z-index: 1;
        display: grid;
        grid-template-columns: minmax(300px, 42%) 1fr;
        grid-template-rows: 1fr;
        transform: translateY(18px) scale(0.985);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.24s ease;
    }

    .event-modal-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #ef4444, #b91c1c);
        z-index: 2;
    }

    .event-modal.is-open .event-modal-card {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    .event-modal-close {
        position: absolute;
        right: 1rem;
        top: 1rem;
        width: 2rem;
        height: 2rem;
        border: 1px solid #3d4a60;
        border-radius: 999px;
        background: rgba(32, 42, 58, 0.92);
        color: #fff;
        font-size: 1.05rem;
        line-height: 1;
        cursor: pointer;
        z-index: 2;
        transition: transform 0.16s ease, filter 0.16s ease;
    }

    .event-modal-close:hover {
        transform: scale(1.05);
        filter: brightness(1.08);
    }

    .event-modal-image {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center left;
        background-color: #e5e7eb;
        position: relative;
    }

    .event-modal-image::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(7, 10, 16, 0.08), rgba(7, 10, 16, 0));
    }

    .event-modal-content {
        padding: 1.25rem 1.25rem 1.35rem;
        overflow: auto;
    }

    .event-modal-category {
        margin: 0;
        color: #b91c1c;
        font-size: 0.72rem;
        letter-spacing: 0.1em;
        font-weight: 700;
        text-transform: uppercase;
    }

    .event-modal-title {
        margin: 0.35rem 0 0.75rem;
        color: #18212b;
        font-size: clamp(1.2rem, 2.6vw, 1.9rem);
        line-height: 1.2;
    }

    .event-modal-description {
        margin: 0 0 1rem;
        color: #38485c;
        font-size: 0.92rem;
        line-height: 1.6;
    }

    .event-modal-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(180px, 1fr));
        gap: 0.48rem 0.72rem;
        margin-bottom: 1rem;
    }

    .event-modal-row {
        margin: 0;
        color: #324861;
        font-size: 0.84rem;
        line-height: 1.4;
        background: #f3f7fc;
        border: 1px solid #dbe4f1;
        border-radius: 10px;
        padding: 0.55rem 0.68rem;
    }

    .event-modal-row strong {
        color: #18212b;
    }

    .event-modal-section {
        margin-bottom: 0.95rem;
    }

    .event-modal-section h4 {
        margin: 0 0 0.45rem;
        color: #1d2b3d;
        font-size: 0.78rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        font-weight: 800;
    }

    .event-modal-artists {
        display: flex;
        flex-wrap: wrap;
        gap: 0.42rem;
    }

    .artist-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        border: 1px solid #c8d6ea;
        background: #eef4fb;
        color: #23374f;
        font-size: 0.76rem;
        font-weight: 600;
        padding: 0.32rem 0.62rem;
    }

    .event-modal-attendees {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0;
    }

    .attendee-item {
        display: inline-flex;
        align-items: center;
        margin-left: -0.45rem;
        z-index: 1;
    }

    .attendee-item:first-child {
        margin-left: 0;
    }

    .attendee-avatar {
        width: 1.65rem;
        height: 1.65rem;
        border-radius: 999px;
        overflow: hidden;
        background: #dbe4f1;
        border: 2px solid #ffffff;
        box-shadow: 0 3px 9px rgba(8, 18, 34, 0.22);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #314359;
        font-size: 0.62rem;
        font-weight: 700;
    }

    .attendee-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .attendee-abbr {
        display: none;
    }

    .attendee-more {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.9rem;
        height: 1.9rem;
        margin-left: -0.35rem;
        border-radius: 999px;
        background: #dbe7f8;
        border: 2px solid #ffffff;
        color: #1e4d8f;
        font-size: 0.74rem;
        font-weight: 700;
        box-shadow: 0 3px 9px rgba(8, 18, 34, 0.18);
    }

    .event-modal-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.52rem 0.95rem;
        background: #ffffff;
        border: 1px solid #cfd9e6;
        color: #1f3247;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
    }

    .event-modal-actions {
        margin-top: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .event-modal-buy {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        border: 0;
        padding: 0.58rem 1.2rem;
        background: linear-gradient(90deg, #ef4444, #b91c1c);
        color: #fff;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 8px 18px rgba(185, 28, 28, 0.35);
    }

    .event-modal-buy:hover,
    .event-modal-link:hover {
        filter: brightness(1.06);
    }

    .event-payment-options {
        margin-top: 0.8rem;
        border: 1px solid #cfd9e8;
        background: #f1f5fb;
        border-radius: 12px;
        padding: 0.8rem;
    }

    .event-payment-title {
        margin: 0;
        color: #1c2a3a;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .event-payment-amount {
        margin: 0.25rem 0 0.6rem;
        color: #39506b;
        font-size: 0.82rem;
    }

    .event-payment-qty-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.7rem;
        margin: 0.3rem 0 0.45rem;
    }

    .event-payment-qty-label {
        color: #22354d;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .event-payment-qty {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border: 1px solid #c9d7ea;
        border-radius: 999px;
        background: #ffffff;
        padding: 0.2rem;
    }

    .qty-btn {
        width: 1.7rem;
        height: 1.7rem;
        border: 1px solid #d6e0ee;
        border-radius: 999px;
        background: #f7faff;
        color: #1f3c62;
        font-size: 0.95rem;
        font-weight: 700;
        line-height: 1;
        cursor: pointer;
    }

    .qty-btn:hover {
        background: #edf4ff;
        border-color: #bcd0ea;
    }

    .qty-value {
        min-width: 1.8rem;
        text-align: center;
        color: #15395f;
        font-size: 0.86rem;
        font-weight: 700;
    }

    .event-payment-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.45rem;
    }

    .payment-option {
        text-align: left;
        border-radius: 10px;
        border: 1px solid #c6d4e9;
        background: #fff;
        color: #173150;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.58rem 0.7rem;
        cursor: pointer;
        transition: border-color 0.16s ease, background 0.16s ease, transform 0.16s ease;
    }

    .payment-option:hover {
        border-color: #2f7df5;
        background: #f1f6ff;
        transform: translateY(-1px);
    }

    .payment-option.is-selected {
        border-color: #2f7df5;
        background: #eaf2ff;
        color: #1b4f97;
        box-shadow: inset 0 0 0 1px rgba(47, 125, 245, 0.28);
    }

    body.modal-open {
        overflow: hidden;
    }

    @media (max-width: 767px) {
        .event-modal {
            padding: 0.6rem;
        }

        .event-modal-card {
            width: 100%;
            max-height: 92vh;
            border-radius: 14px;
            transform: translateY(30px);
            grid-template-rows: minmax(220px, 36vh) 1fr;
            grid-template-columns: 1fr;
        }

        .event-modal.is-open .event-modal-card {
            transform: translateY(0);
        }

        .event-modal-meta-grid {
            grid-template-columns: 1fr;
            gap: 0.25rem;
        }
    }

    /* responsive */
    @media (max-width: 1024px) {
        .upcoming-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 767px) {
        .upcoming-events {
            padding: 2rem 0.75rem 2.5rem;
        }

        .upcoming-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
    }

    @media (max-width: 480px) {
        .upcoming-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    (() => {
        const modal = document.getElementById('event-modal');
        const modalImage = document.getElementById('event-modal-image');
        const modalCategory = document.getElementById('event-modal-category');
        const modalTitle = document.getElementById('event-modal-title');
        const modalDate = document.getElementById('event-modal-date');
        const modalTime = document.getElementById('event-modal-time');
        const modalLocation = document.getElementById('event-modal-location');
        const modalPrice = document.getElementById('event-modal-price');
        const modalLink = document.getElementById('event-modal-link');
        const modalDescription = document.getElementById('event-modal-description');
        const modalArtists = document.getElementById('event-modal-artists');
        const modalAttendees = document.getElementById('event-modal-attendees');
        const categoryPills = document.querySelectorAll('.category-pill');
        const upcomingCards = document.querySelectorAll('.ucard');
        const modalBuy = document.getElementById('event-modal-buy');
        const paymentOptions = document.getElementById('event-payment-options');
        const paymentAmount = document.getElementById('event-payment-amount');
        const paymentOptionButtons = document.querySelectorAll('.payment-option');
        const ticketQtyMinus = document.getElementById('ticket-qty-minus');
        const ticketQtyPlus = document.getElementById('ticket-qty-plus');
        const ticketQtyValue = document.getElementById('ticket-qty-value');

        let currentUnitPrice = 0;
        let currentQty = 1;

        if (!modal) return;

        const getBackgroundUrl = (el) => {
            if (!el) return '';
            const bg = getComputedStyle(el).backgroundImage || '';
            const match = bg.match(/url\(["']?(.*?)["']?\)/);
            return match ? match[1] : '';
        };

        const parsePriceToNumber = (priceText) => {
            if (!priceText) return 0;
            if (/free/i.test(priceText)) return 0;
            const cleaned = priceText.replace(/[^0-9.]/g, '');
            return cleaned ? Number(cleaned) : 0;
        };

        const formatUgx = (amount) => {
            if (!amount || Number.isNaN(amount)) return 'Free';
            return `UGX ${Math.round(amount).toLocaleString('en-US')}`;
        };

        const updatePaymentTotal = () => {
            if (!paymentAmount || !ticketQtyValue) return;
            ticketQtyValue.textContent = String(currentQty);
            paymentAmount.textContent = formatUgx(currentUnitPrice * currentQty);
        };

        const cardMatchesCategory = (card, category) => {
            if (!category) return false;
            if (category === 'all') return true;
            if (category === 'free') {
                const priceText = card.querySelector('.ucard-price')?.textContent || '';
                return /free/i.test(priceText);
            }

            return (card.dataset.category || '').toLowerCase() === category;
        };

        const setActiveCategory = (category) => {
            categoryPills.forEach((pill) => {
                const isActive = (pill.dataset.category || '') === category;
                pill.classList.toggle('is-active', isActive);
                pill.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            });

            upcomingCards.forEach((card) => {
                const show = cardMatchesCategory(card, category);
                card.classList.toggle('is-hidden', !show);
            });
        };

        categoryPills.forEach((pill) => {
            const category = (pill.dataset.category || '').toLowerCase();
            if (!category) return;

            pill.addEventListener('click', () => setActiveCategory(category));
            pill.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter' && event.key !== ' ') return;
                event.preventDefault();
                setActiveCategory(category);
            });
        });

        const initiallyActiveCategory = document.querySelector('.category-pill.is-active')?.dataset.category;
        if (initiallyActiveCategory) {
            setActiveCategory(initiallyActiveCategory.toLowerCase());
        }

        const eventExtras = {
            'Live Music Night': {
                description: 'An electrifying evening of live sound, crowd anthems, and curated stage moments featuring top local voices and guest performers.',
                artists: ['Ava Keys', 'DJ Roni', 'The Echo Band', 'MC Zion', 'among others'],
                attendees: [
                    { abbr: 'OS', avatar: '/images/music.jpg' },
                    { abbr: 'KL', avatar: '/images/hero-image2.jpg' },
                    { abbr: 'NT', avatar: '/images/hero-image3.jfif' },
                    { abbr: 'FA', avatar: '/images/movie.jpg' },
                ],
                attendeeMore: '+48',
            },
            'Business Connect 2026': {
                description: 'A premium networking and knowledge-sharing session for founders, operators, and teams building high-impact products and ventures.',
                artists: ['Keynote Panel', 'Growth Leaders', 'Startup Circle', 'Industry Guests', 'among others'],
                attendees: [
                    { abbr: 'OS', avatar: '/images/conference.jpg' },
                    { abbr: 'KL', avatar: '/images/skilling.jpg' },
                    { abbr: 'DM', avatar: '/images/music.jpg' },
                    { abbr: 'HN', avatar: '/images/socker.jpg' },
                ],
                attendeeMore: '+62',
            },
            'Film Premiere Weekend': {
                description: 'A weekend showcase of fresh cinema with cast appearances, audience Q&A, and immersive screening sessions across genres.',
                artists: ['Host Panel', 'Directors Circle', 'Screen Talent', 'Creative Crew', 'among others'],
                attendees: [
                    { abbr: 'OS', avatar: '/images/movie.jpg' },
                    { abbr: 'KL', avatar: '/images/conference.jpg' },
                    { abbr: 'JP', avatar: '/images/home-hero-bg.jpg' },
                    { abbr: 'RB', avatar: '/images/music.jpg' },
                ],
                attendeeMore: '+33',
            },
            'City Sports Clash': {
                description: 'A high-energy sports showdown featuring local clubs, roaring supporters, and live entertainment from kickoff to final whistle.',
                artists: ['Game Hosts', 'Arena DJs', 'Fan Zone Crew', 'Special Guests', 'among others'],
                attendees: [
                    { abbr: 'OS', avatar: '/images/socker.jpg' },
                    { abbr: 'KL', avatar: '/images/music.jpg' },
                    { abbr: 'LM', avatar: '/images/hero-image2.jpg' },
                    { abbr: 'QA', avatar: '/images/conference.jpg' },
                ],
                attendeeMore: '+71',
            },
            'Skills Bootcamp': {
                description: 'Hands-on learning tracks, practical coaching, and collaborative sessions designed to sharpen skills and accelerate career growth.',
                artists: ['Lead Coaches', 'Mentor Team', 'Lab Facilitators', 'Guest Trainers', 'among others'],
                attendees: [
                    { abbr: 'OS', avatar: '/images/skilling.jpg' },
                    { abbr: 'KL', avatar: '/images/conference.jpg' },
                    { abbr: 'GS', avatar: '/images/movie.jpg' },
                    { abbr: 'TA', avatar: '/images/hero-image3.jfif' },
                ],
                attendeeMore: '+26',
            },
            'Laugh Factory Kampala': {
                description: 'Back-to-back stand-up sets with sharp storytelling, crowd interaction, and unforgettable comedy moments all night long.',
                artists: ['Comedian JO', 'Maya K', 'Setline Crew', 'Open Mic Picks', 'among others'],
                attendees: [
                    { abbr: 'OS', avatar: '/images/home-hero-bg.jpg' },
                    { abbr: 'KL', avatar: '/images/music.jpg' },
                    { abbr: 'VR', avatar: '/images/conference.jpg' },
                    { abbr: 'PT', avatar: '/images/hero-image2.jpg' },
                ],
                attendeeMore: '+39',
            },
            'Midnight Stage Play': {
                description: 'A dramatic theatrical performance blending live acting, immersive lighting, and contemporary storytelling in one intimate night.',
                artists: ['Stage Ensemble', 'Lead Cast', 'Choreo Unit', 'Voice Team', 'among others'],
                attendees: [
                    { abbr: 'OS', avatar: '/images/hero-image2.jpg' },
                    { abbr: 'KL', avatar: '/images/movie.jpg' },
                    { abbr: 'RT', avatar: '/images/music.jpg' },
                    { abbr: 'BW', avatar: '/images/conference.jpg' },
                ],
                attendeeMore: '+21',
            },
            'Neighbourhood Fest': {
                description: 'A vibrant community celebration with food, performances, local creators, and shared experiences for families and friends.',
                artists: ['Community Band', 'Culture Troupe', 'Festival Hosts', 'Guest Voices', 'among others'],
                attendees: [
                    { abbr: 'OS', avatar: '/images/hero-image3.jfif' },
                    { abbr: 'KL', avatar: '/images/skilling.jpg' },
                    { abbr: 'MR', avatar: '/images/music.jpg' },
                    { abbr: 'AX', avatar: '/images/socker.jpg' },
                ],
                attendeeMore: '+54',
            },
            __default: {
                description: 'Join this event for a complete experience with curated sessions, engaging moments, and a vibrant attendee community.',
                artists: ['Featured Guests', 'Main Hosts', 'Curated Lineup', 'among others'],
                attendees: [
                    { abbr: 'OS', avatar: '/images/home-hero-bg.jpg' },
                    { abbr: 'KL', avatar: '/images/hero-image2.jpg' },
                    { abbr: 'NT', avatar: '/images/hero-image3.jfif' },
                ],
                attendeeMore: '+12',
            },
        };

        const enrichData = (data) => {
            const extra = eventExtras[data.title] || eventExtras.__default;
            return {
                ...data,
                description: extra.description,
                artists: extra.artists,
                attendees: extra.attendees,
                attendeeMore: extra.attendeeMore,
            };
        };

        const openModal = (data) => {
            modalImage.style.backgroundImage = data.image ? `url('${data.image}')` : 'none';
            modalCategory.textContent = data.category || 'Event';
            modalTitle.textContent = data.title || 'Event Details';
            modalDate.textContent = data.date || 'Upcoming';
            modalTime.textContent = data.time || 'TBA';
            modalLocation.textContent = data.location || 'Venue details available';
            modalPrice.textContent = data.price || 'Check listing';
            modalDescription.textContent = data.description || 'Event details and experience information will appear here.';
            modalLink.href = data.href || '/events';

            currentUnitPrice = parsePriceToNumber(data.price || '0');
            currentQty = 1;
            updatePaymentTotal();

            if (paymentOptions) {
                paymentOptions.hidden = true;
            }

            modalArtists.innerHTML = '';
            (data.artists || []).forEach((artist) => {
                const chip = document.createElement('span');
                chip.className = 'artist-chip';
                chip.textContent = artist;
                modalArtists.appendChild(chip);
            });

            modalAttendees.innerHTML = '';
            (data.attendees || []).forEach((person) => {
                const item = document.createElement('span');
                item.className = 'attendee-item';

                const avatar = document.createElement('span');
                avatar.className = 'attendee-avatar';
                avatar.title = person.abbr || 'Guest';

                if (person.avatar) {
                    const img = document.createElement('img');
                    img.src = person.avatar;
                    img.alt = `${person.abbr || 'Guest'} avatar`;
                    avatar.appendChild(img);
                } else {
                    avatar.textContent = person.abbr || 'GU';
                }

                item.appendChild(avatar);
                modalAttendees.appendChild(item);
            });

            if (data.attendeeMore) {
                const more = document.createElement('span');
                more.className = 'attendee-more';
                more.textContent = data.attendeeMore;
                modalAttendees.appendChild(more);
            }

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
        };

        const closeModal = () => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
            if (paymentOptions) {
                paymentOptions.hidden = true;
            }
            paymentOptionButtons.forEach((btn) => btn.classList.remove('is-selected'));
            currentQty = 1;
        };

        const buildFromTrending = (card) => {
            const title = card.querySelector('h3')?.textContent?.trim();
            const category = card.querySelector('p')?.textContent?.trim();
            const image = getBackgroundUrl(card.querySelector('.event-thumb'));

            return {
                title,
                category,
                date: 'Upcoming',
                time: 'To be announced',
                location: 'See full listing for venue details',
                price: 'From UGX 20,000',
                image,
                href: '/events',
            };
        };

        const buildFromUpcoming = (card) => {
            const title = card.querySelector('.ucard-title')?.textContent?.trim();
            const category = card.querySelector('.ucard-cat')?.textContent?.trim();
            const day = card.querySelector('.ucard-day')?.textContent?.trim();
            const mon = card.querySelector('.ucard-mon')?.textContent?.trim();
            const metas = card.querySelectorAll('.ucard-meta');
            const location = metas[0]?.textContent?.trim();
            const time = metas[1]?.textContent?.trim();
            const price = card.querySelector('.ucard-price')?.textContent?.trim();
            const href = card.querySelector('.ucard-btn')?.getAttribute('href') || '/events';
            const image = getBackgroundUrl(card.querySelector('.ucard-thumb'));

            return {
                title,
                category,
                date: day && mon ? `${day} ${mon}` : 'Upcoming',
                time,
                location,
                price,
                image,
                href,
            };
        };

        document.querySelectorAll('.event-card, .ucard').forEach((card) => {
            card.setAttribute('tabindex', '0');

            card.addEventListener('click', (event) => {
                if (event.target.closest('a')) return;
                const data = card.classList.contains('ucard') ? buildFromUpcoming(card) : buildFromTrending(card);
                openModal(enrichData(data));
            });

            card.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter' && event.key !== ' ') return;
                event.preventDefault();
                const data = card.classList.contains('ucard') ? buildFromUpcoming(card) : buildFromTrending(card);
                openModal(enrichData(data));
            });
        });

        modal.querySelectorAll('[data-modal-close]').forEach((el) => {
            el.addEventListener('click', closeModal);
        });

        if (modalBuy && paymentOptions) {
            modalBuy.addEventListener('click', () => {
                paymentOptions.hidden = !paymentOptions.hidden;
                if (!paymentOptions.hidden) {
                    updatePaymentTotal();
                }
            });
        }

        if (ticketQtyPlus) {
            ticketQtyPlus.addEventListener('click', () => {
                currentQty += 1;
                updatePaymentTotal();
            });
        }

        if (ticketQtyMinus) {
            ticketQtyMinus.addEventListener('click', () => {
                if (currentQty <= 1) return;
                currentQty -= 1;
                updatePaymentTotal();
            });
        }

        paymentOptionButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                paymentOptionButtons.forEach((item) => item.classList.remove('is-selected'));
                btn.classList.add('is-selected');
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                closeModal();
            }
        });
    })();
</script>
    <style>
        html, body {
            margin: 0;
            padding: 0;
        }

        .home-hero {
            --header-h: 92px;
            height: calc(100vh - var(--header-h) - 58px); /* 58px = marquee height */
            max-height: calc(100vh - var(--header-h) - 58px);
            min-height: 400px;
            overflow: hidden;
            display: block;
            padding: 0;
            background: transparent;
        }

        .hero-shell {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .hero-surface {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            border: 0;
            box-shadow: none;
            border-radius: 0;
        }

        .hero-slides {
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .hero-slide {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
            opacity: 0;
            transition: opacity 0.8s ease;
        }

        .hero-slide.is-active {
            opacity: 1;
        }

        .hero-copy {
            position: relative;
            z-index: 2;
            color: #fff;
            max-width: 560px;
            padding: 5rem 1.5rem 2rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.45);
        }

        .hero-brand {
            margin: 0 0 1rem;
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            opacity: 0.95;
        }

        .hero-title {
            margin: 0;
            font-size: clamp(1.9rem, 4.8vw, 3.6rem);
            line-height: 0.95;
            letter-spacing: -0.03em;
            font-weight: 800;
        }

        .hero-kicker {
            margin: 1.1rem 0 0.45rem;
            font-size: 0.76rem;
            letter-spacing: 0.1em;
            opacity: 0.85;
        }

        .hero-description {
            margin: 0;
            font-size: clamp(0.92rem, 1.8vw, 1.1rem);
            line-height: 1.5;
            font-weight: 500;
        }

        .hero-description-line {
            display: block;
        }

        .hero-cta {
            margin-top: 1.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            border-radius: 999px;
            padding: 0.62rem 1.4rem;
            background: linear-gradient(90deg, #ef4444, #b91c1c);
            box-shadow: 0 10px 30px rgba(185, 28, 28, 0.45);
        }

        .hero-cta:hover {
            filter: brightness(1.08);
        }

        /* — marquee — */
        .categories-marquee {
            position: relative;
            background: linear-gradient(180deg, rgba(7,7,8,0.96), rgba(10,10,11,0.98));
            border-top: 1px solid rgba(255,255,255,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding: 0.9rem 0;
            overflow: hidden;
        }

        .categories-shell {
            width: min(1280px, 100%);
            margin: 0 auto;
            padding: 0 0.5rem;
        }

        .categories-track-wrap {
            overflow-x: auto;
            scrollbar-width: thin;
        }

        .categories-track {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            min-width: max-content;
            padding: 0.1rem 0.2rem;
        }

        .category-pill {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.12rem;
            min-width: 96px;
            white-space: nowrap;
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
            color: #dce7f6;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            background: #0e1726;
            border: 1px solid #233248;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.28);
            backdrop-filter: blur(6px);
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        .category-pill:hover {
            background: #132238;
            border-color: #2f4f7c;
            color: #eef5ff;
        }

        .category-pill.is-active {
            background: linear-gradient(180deg, #2f7df5, #1e5ed6);
            border-color: #2f7df5;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(39, 110, 230, 0.35);
        }

        .category-pill-top {
            display: inline-flex;
            align-items: center;
            gap: 0.34rem;
            line-height: 1;
        }

        .category-pill-sub {
            font-size: 0.64rem;
            font-weight: 500;
            color: #8fa2bb;
            line-height: 1;
        }

        .category-pill.is-active .category-pill-sub {
            color: rgba(236, 244, 255, 0.95);
        }

        .icon-sprite {
            position: absolute;
            width: 0;
            height: 0;
            overflow: hidden;
        }

        .cat-icon {
            width: 1rem;
            height: 1rem;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.9;
            stroke-linecap: round;
            stroke-linejoin: round;
            opacity: 0.92;
        }

        .trending-events {
            background: linear-gradient(180deg, #e8eef4 0%, #dde6ee 100%);
            padding: 2.5rem 1rem 3.25rem;
        }

        .trending-shell {
            width: min(1180px, 100%);
            margin: 0 auto;
        }

        .trending-head {
            text-align: center;
            margin-bottom: 1.2rem;
        }

        .trending-kicker {
            margin: 0;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: #516174;
        }

        .trending-title {
            margin: 0.35rem 0 0;
            color: #18212b;
            font-size: clamp(1.4rem, 2.4vw, 2rem);
            line-height: 1.1;
        }

        .trending-grid {
            display: flex;
            flex-wrap: nowrap;
            gap: 1rem;
            align-items: end;
        }

        .event-card {
            flex: 1 1 0;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(80, 98, 118, 0.18);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 12px 24px rgba(34, 52, 72, 0.15);
            transform: translateY(0);
            transition: transform 0.25s ease, box-shadow 0.25s ease, flex-grow 0.28s ease;
            backdrop-filter: blur(4px);
        }

        .event-card:hover {
            flex-grow: 1.45;
            transform: translateY(-6px);
            box-shadow: 0 18px 30px rgba(22, 35, 50, 0.22);
        }

        .event-card-featured {
            flex-grow: 1.15;
            transform: translateY(-14px);
            box-shadow: 0 20px 34px rgba(20, 34, 50, 0.25);
        }

        .event-card-featured:hover {
            transform: translateY(-18px);
        }

        .event-thumb {
            width: 100%;
            aspect-ratio: 16 / 10;
            background-size: cover;
            background-position: center;
        }

        .event-content {
            padding: 0.8rem 0.85rem 1rem;
        }

        .event-content h3 {
            margin: 0;
            color: #18212b;
            font-size: 0.96rem;
            line-height: 1.2;
        }

        .event-content p {
            margin: 0.35rem 0 0;
            color: #5b6979;
            font-size: 0.78rem;
            line-height: 1.35;
        }

        /* — responsive — */
        @media (min-width: 768px) {
            .hero-copy {
                padding: 6rem 3rem 2rem;
            }
        }

        @media (max-width: 767px) {
            .home-hero {
                --header-h: 82px;
                height: calc(100vh - var(--header-h) - 52px);
                max-height: calc(100vh - var(--header-h) - 52px);
            }

            .categories-track {
                gap: 0.6rem;
                animation-duration: 26s;
            }

            .category-pill {
                font-size: 0.82rem;
                padding: 0.45rem 0.8rem;
            }

            .trending-events {
                padding: 2rem 0.75rem 2.5rem;
            }

            .trending-grid {
                display: grid;
                grid-auto-flow: column;
                grid-auto-columns: minmax(220px, 72vw);
                overflow-x: auto;
                padding-bottom: 0.4rem;
                scrollbar-width: thin;
            }

            .event-card-featured {
                transform: translateY(0);
            }

            .event-card-featured:hover {
                transform: translateY(-6px);
            }
        }
    </style>

    <script>
        (() => {
            const slides = document.querySelectorAll('.hero-slide');
            if (slides.length < 2) return;

            let index = 0;
            setInterval(() => {
                slides[index].classList.remove('is-active');
                index = (index + 1) % slides.length;
                slides[index].classList.add('is-active');
            }, 4000);
        })();
    </script>
@endsection
