@extends('layouts.app')

@section('content')
<section style="min-height:100vh;padding:8.5rem 1rem 4rem;background:#07101c;">
    <div style="width:min(980px,calc(100% - 2rem));margin:0 auto;display:grid;grid-template-columns:1.2fr .8fr;gap:1rem;">
        <article style="background:#111827;border:1px solid #1f2937;border-radius:14px;overflow:hidden;">
            @php
                $bannerPath = trim((string) $event->image_url);
                if ($bannerPath === '') {
                    $bannerImageUrl = asset('images/movie.jpg');
                } elseif (str_starts_with($bannerPath, 'http://') || str_starts_with($bannerPath, 'https://')) {
                    $bannerImageUrl = $bannerPath;
                } else {
                    $normalizedBannerPath = ltrim($bannerPath, '/');
                    if (str_starts_with($normalizedBannerPath, 'storage/') || str_starts_with($normalizedBannerPath, 'images/')) {
                        $bannerImageUrl = asset($normalizedBannerPath);
                    } elseif (str_starts_with($normalizedBannerPath, 'event-images/')) {
                        $bannerImageUrl = asset('storage/' . $normalizedBannerPath);
                    } else {
                        $bannerImageUrl = asset($normalizedBannerPath);
                    }
                }
            @endphp

            <div style="height:220px;background:linear-gradient(rgba(7,16,28,0.3),rgba(7,16,28,0.75)), url('{{ $bannerImageUrl }}') center/cover no-repeat;"></div>

            <div style="padding:1rem 1rem 1.15rem;">
                @if (session('success'))
                    <div style="margin-bottom:.9rem;padding:.7rem .8rem;border-radius:9px;background:#052e1f;border:1px solid #166534;color:#bbf7d0;font-size:.88rem;">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div style="margin-bottom:.9rem;padding:.7rem .8rem;border-radius:9px;background:#3f0f1a;border:1px solid #7f1d1d;color:#fecaca;font-size:.88rem;">
                        {{ session('error') }}
                    </div>
                @endif

                <p style="margin:0 0 .4rem;color:#93c5fd;font-size:.74rem;letter-spacing:.08em;text-transform:uppercase;font-weight:700;">
                    {{ strtoupper($event->category?->name ?? 'EVENT') }}
                </p>
                <h1 style="margin:0;color:#fff;font-size:1.6rem;font-weight:800;line-height:1.25;">{{ $event->title }}</h1>

                <div style="margin-top:.7rem;color:#9ca3af;font-size:.92rem;line-height:1.5;">
                    <div>{{ $event->starts_at?->format('l, d M Y · h:i A') }}</div>
                    <div>{{ $event->venue }}, {{ $event->city }}, {{ $event->country }}</div>
                </div>

                <p style="margin-top:1rem;color:#d1d5db;line-height:1.65;font-size:.95rem;">{{ $event->description }}</p>
            </div>
        </article>

        <aside style="background:#111827;border:1px solid #1f2937;border-radius:14px;padding:1rem;align-self:start;">
            <h2 style="margin:0 0 .6rem;color:#fff;font-size:1.05rem;font-weight:800;">Gate Sales Event</h2>
            <p style="margin:0;color:#d1d5db;font-size:.92rem;line-height:1.6;">
                This event uses <strong style="color:#fff;">physical gate sales</strong>. Tickets or wristbands are sold at the venue gate and verified on-site.
            </p>

            <div style="margin-top:.9rem;padding:.75rem;border-radius:10px;background:#0f172a;border:1px solid #1e293b;">
                <p style="margin:0;color:#93c5fd;font-size:.74rem;text-transform:uppercase;letter-spacing:.08em;font-weight:700;">Package</p>
                <p style="margin:.35rem 0 0;color:#fff;font-size:.95rem;font-weight:700;">
                    {{ $event->service_package === 'premium_wristbands' ? 'Premium wristbands' : 'Batch printed tickets' }}
                </p>
            </div>

            <div style="margin-top:1rem;display:grid;gap:.5rem;">
                <a href="{{ route('events.index') }}" style="display:block;text-align:center;padding:.65rem .8rem;border-radius:9px;background:#1d4ed8;color:#fff;text-decoration:none;font-weight:700;">
                    Browse Other Events
                </a>
                <a href="{{ route('contact') }}" style="display:block;text-align:center;padding:.65rem .8rem;border-radius:9px;background:#1f2937;color:#fff;text-decoration:none;font-weight:700;">
                    Contact Support
                </a>
            </div>
        </aside>
    </div>
</section>
@endsection
