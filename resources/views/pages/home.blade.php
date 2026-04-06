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
                        <span class="hero-description-line">Built for local event creators—concerts, theaters, schools, workshops, and more.</span>
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
            </svg>

            <div class="categories-track-wrap">
                <div class="categories-track" role="list">
                    <span class="category-pill" role="listitem"><svg class="cat-icon" aria-hidden="true"><use href="#icon-music"></use></svg> Concerts &amp; Music Shows</span>
                    <span class="category-pill" role="listitem"><svg class="cat-icon" aria-hidden="true"><use href="#icon-sports"></use></svg> Sports &amp; Games</span>
                    <span class="category-pill" role="listitem"><svg class="cat-icon" aria-hidden="true"><use href="#icon-theater"></use></svg> Theater &amp; Arts</span>
                    <span class="category-pill" role="listitem"><svg class="cat-icon" aria-hidden="true"><use href="#icon-briefcase"></use></svg> Conferences &amp; Corporate Events</span>
                    <span class="category-pill" role="listitem"><svg class="cat-icon" aria-hidden="true"><use href="#icon-tools"></use></svg> Workshops &amp; Training</span>
                    <span class="category-pill" role="listitem"><svg class="cat-icon" aria-hidden="true"><use href="#icon-community"></use></svg> Community &amp; Religious Events</span>
                    <span class="category-pill" role="listitem"><svg class="cat-icon" aria-hidden="true"><use href="#icon-comedy"></use></svg> Comedy &amp; Entertainment Nights</span>
                    <span class="category-pill" role="listitem"><svg class="cat-icon" aria-hidden="true"><use href="#icon-film"></use></svg> Film &amp; Screenings</span>

                    <span class="category-pill" aria-hidden="true"><svg class="cat-icon" aria-hidden="true"><use href="#icon-music"></use></svg> Concerts &amp; Music Shows</span>
                    <span class="category-pill" aria-hidden="true"><svg class="cat-icon" aria-hidden="true"><use href="#icon-sports"></use></svg> Sports &amp; Games</span>
                    <span class="category-pill" aria-hidden="true"><svg class="cat-icon" aria-hidden="true"><use href="#icon-theater"></use></svg> Theater &amp; Arts</span>
                    <span class="category-pill" aria-hidden="true"><svg class="cat-icon" aria-hidden="true"><use href="#icon-briefcase"></use></svg> Conferences &amp; Corporate Events</span>
                    <span class="category-pill" aria-hidden="true"><svg class="cat-icon" aria-hidden="true"><use href="#icon-tools"></use></svg> Workshops &amp; Training</span>
                    <span class="category-pill" aria-hidden="true"><svg class="cat-icon" aria-hidden="true"><use href="#icon-community"></use></svg> Community &amp; Religious Events</span>
                    <span class="category-pill" aria-hidden="true"><svg class="cat-icon" aria-hidden="true"><use href="#icon-comedy"></use></svg> Comedy &amp; Entertainment Nights</span>
                    <span class="category-pill" aria-hidden="true"><svg class="cat-icon" aria-hidden="true"><use href="#icon-film"></use></svg> Film &amp; Screenings</span>
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
            overflow: hidden;
            mask-image: linear-gradient(to right, transparent 0, black 8%, black 92%, transparent 100%);
            -webkit-mask-image: linear-gradient(to right, transparent 0, black 8%, black 92%, transparent 100%);
        }

        .categories-track {
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            min-width: max-content;
            animation: categoriesMarquee 34s linear infinite;
        }

        .category-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            color: #f7f7f8;
            font-size: 0.88rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            background: linear-gradient(145deg, rgba(255,255,255,0.18), rgba(255,255,255,0.08));
            border: 1px solid rgba(255,255,255,0.28);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.16), 0 8px 20px rgba(0,0,0,0.28);
            backdrop-filter: blur(8px);
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
            opacity: 0.95;
        }

        @keyframes categoriesMarquee {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
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