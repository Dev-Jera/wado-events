<div style="display:flex; flex-direction:column; gap:1rem; padding:.25rem 0;">

    {{-- Status + package --}}
    <div style="display:flex; align-items:center; gap:.6rem; flex-wrap:wrap;">
        <span style="
            display:inline-flex; align-items:center;
            background:rgba(232,160,106,.15); border:1px solid rgba(232,160,106,.4);
            color:#e8a06a; font-size:.72rem; font-weight:700; letter-spacing:.05em;
            border-radius:999px; padding:.28rem .8rem; text-transform:uppercase;">
            {{ $enquiry->package }}
        </span>
        @php
            $statusColor = match($enquiry->status) {
                'New'     => ['bg' => 'rgba(59,130,246,.12)', 'border' => 'rgba(59,130,246,.35)', 'text' => '#93c5fd'],
                'Replied' => ['bg' => 'rgba(34,197,94,.12)',  'border' => 'rgba(34,197,94,.35)',  'text' => '#86efac'],
                default   => ['bg' => 'rgba(107,114,128,.12)','border' => 'rgba(107,114,128,.3)', 'text' => '#d1d5db'],
            };
        @endphp
        <span style="
            display:inline-flex; align-items:center;
            background:{{ $statusColor['bg'] }}; border:1px solid {{ $statusColor['border'] }};
            color:{{ $statusColor['text'] }}; font-size:.72rem; font-weight:700;
            border-radius:999px; padding:.28rem .8rem;">
            {{ $enquiry->status }}
        </span>
    </div>

    {{-- Contact info --}}
    <div style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.09); border-radius:12px; padding:1rem 1.1rem;">
        <p style="margin:0 0 .55rem; font-size:.68rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:rgba(255,255,255,.4);">Contact</p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:.5rem .75rem;">
            <div>
                <p style="margin:0 0 .15rem; font-size:.72rem; color:rgba(255,255,255,.45);">Name</p>
                <p style="margin:0; font-size:.92rem; font-weight:600; color:#fff;">{{ $enquiry->name }}</p>
            </div>
            <div>
                <p style="margin:0 0 .15rem; font-size:.72rem; color:rgba(255,255,255,.45);">Email</p>
                <p style="margin:0; font-size:.92rem; color:#fff;">
                    <a href="mailto:{{ $enquiry->email }}" style="color:#93c5fd; text-decoration:none;">{{ $enquiry->email }}</a>
                </p>
            </div>
            @if ($enquiry->phone)
            <div>
                <p style="margin:0 0 .15rem; font-size:.72rem; color:rgba(255,255,255,.45);">Phone</p>
                <p style="margin:0; font-size:.92rem; color:#fff;">{{ $enquiry->phone }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Event details --}}
    @if ($enquiry->event_date || $enquiry->attendance)
    <div style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.09); border-radius:12px; padding:1rem 1.1rem;">
        <p style="margin:0 0 .55rem; font-size:.68rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:rgba(255,255,255,.4);">Event Details</p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:.5rem .75rem;">
            @if ($enquiry->event_date)
            <div>
                <p style="margin:0 0 .15rem; font-size:.72rem; color:rgba(255,255,255,.45);">Date</p>
                <p style="margin:0; font-size:.92rem; color:#fff;">{{ $enquiry->event_date->format('d M Y') }}</p>
            </div>
            @endif
            @if ($enquiry->attendance)
            <div>
                <p style="margin:0 0 .15rem; font-size:.72rem; color:rgba(255,255,255,.45);">Expected Attendance</p>
                <p style="margin:0; font-size:.92rem; color:#fff;">{{ $enquiry->attendance }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Message --}}
    @if ($enquiry->message)
    <div style="background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.09); border-radius:12px; padding:1rem 1.1rem;">
        <p style="margin:0 0 .55rem; font-size:.68rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:rgba(255,255,255,.4);">Message</p>
        <p style="margin:0; font-size:.92rem; color:rgba(255,255,255,.8); line-height:1.7; white-space:pre-wrap;">{{ $enquiry->message }}</p>
    </div>
    @endif

    {{-- Timestamps --}}
    <div style="display:flex; gap:1.5rem; flex-wrap:wrap; padding:.1rem 0;">
        <div>
            <p style="margin:0 0 .1rem; font-size:.7rem; color:rgba(255,255,255,.35);">Received</p>
            <p style="margin:0; font-size:.82rem; color:rgba(255,255,255,.55);">{{ $enquiry->created_at->format('d M Y, H:i') }}</p>
        </div>
        @if ($enquiry->replied_at)
        <div>
            <p style="margin:0 0 .1rem; font-size:.7rem; color:rgba(255,255,255,.35);">Replied</p>
            <p style="margin:0; font-size:.82rem; color:#86efac;">{{ $enquiry->replied_at->format('d M Y, H:i') }}</p>
        </div>
        @endif
    </div>
</div>
