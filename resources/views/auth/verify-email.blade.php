@extends('layouts.app')

@section('content')
<section style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:6rem 1rem 3rem;background:#fdf8f8;">
    <div style="width:min(480px,100%);background:#fff;border-radius:18px;box-shadow:0 2px 24px rgba(192,40,60,.08);border:1px solid #f0dde0;overflow:hidden;">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#c0283c 0%,#8a1525 100%);padding:32px 36px;text-align:center;">
            <div style="font-size:2rem;margin-bottom:10px;">✉️</div>
            <h1 style="margin:0;color:#fff;font-size:1.3rem;font-weight:800;">Check your inbox</h1>
            <p style="margin:8px 0 0;color:rgba(255,255,255,.78);font-size:.87rem;">We sent a verification link to<br><strong>{{ auth()->user()->email }}</strong></p>
        </div>

        {{-- Body --}}
        <div style="padding:28px 36px 32px;">

            @if (session('status'))
                <div style="background:#edf9f2;border:1px solid #bde8cb;border-radius:10px;padding:12px 16px;margin-bottom:20px;color:#136f45;font-size:.85rem;font-weight:600;">
                    {{ session('status') }}
                </div>
            @endif

            <p style="margin:0 0 20px;color:#6b3040;font-size:.88rem;line-height:1.65;">
                Click the link in the email to verify your address. Once verified your tickets and payment confirmations will be delivered without any issues.
            </p>

            <p style="margin:0 0 6px;color:#9a5060;font-size:.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;">Didn't get it?</p>
            <p style="margin:0 0 16px;color:#b08090;font-size:.82rem;">Check your spam folder, or resend below.</p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    style="width:100%;border:0;border-radius:10px;background:#c0283c;color:#fff;font-size:.9rem;font-weight:700;padding:13px;cursor:pointer;letter-spacing:.01em;">
                    Resend verification email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="margin-top:12px;">
                @csrf
                <button type="submit"
                    style="width:100%;border:1px solid #f0dde0;border-radius:10px;background:#fff;color:#9a5060;font-size:.85rem;font-weight:600;padding:11px;cursor:pointer;">
                    Log out
                </button>
            </form>
        </div>

    </div>
</section>
@endsection
