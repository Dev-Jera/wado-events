<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Preview</title>
    <style>
        :root { --site-font: 'Plus Jakarta Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { margin: 0; background: #fff; }
        .tkd-wrap{font-family:var(--site-font);max-width:100%;margin:0;padding:0;}
        .tkd-card{border-radius:0;overflow:hidden;border:0;box-shadow:none;}
        .tkd-card-cancelled{border-color:rgba(220,38,38,.25);}
        .tkd-top-band{height:5px;background:linear-gradient(90deg,#2563EB 0%,#7C3AED 50%,#DC2626 100%);}
        .tkd-bottom-band{height:3px;background:linear-gradient(90deg,#2563EB,#7C3AED,#DC2626);opacity:.55;}
        .tkd-band-danger{background:linear-gradient(90deg,#DC2626,#991B1B) !important;}
        .tkd-alert{display:flex;align-items:center;gap:10px;background:rgba(220,38,38,.12);border-bottom:1px solid rgba(220,38,38,.2);color:#f87171;font-size:.76rem;font-weight:600;padding:10px 20px;}
        .tkd-hero{background:#0c1b5e;display:grid;grid-template-columns:1fr 240px;min-height:230px;position:relative;overflow:hidden;}
        .tkd-hero-bg{position:absolute;inset:0;pointer-events:none;}
        .tkd-hero-left{padding:26px 26px 22px;position:relative;z-index:1;display:flex;flex-direction:column;justify-content:space-between;}
        .tkd-status-pill{display:inline-flex;align-items:center;gap:6px;font-size:.65rem;font-weight:700;letter-spacing:.06em;padding:4px 12px;border-radius:999px;border:1px solid;width:fit-content;margin-bottom:12px;}
        .tkd-pill-green{background:rgba(34,197,94,.13);border-color:rgba(34,197,94,.3);color:#4ade80;}
        .tkd-pill-gray{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.18);color:rgba(255,255,255,.6);}
        .tkd-pill-danger{background:rgba(220,38,38,.18);border-color:rgba(220,38,38,.35);color:#f87171;}
        .tkd-status-dot{width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0;}
        .tkd-title{font-size:clamp(18px,3vw,24px);font-weight:700;color:#fff;line-height:1.15;letter-spacing:-.4px;margin:0 0 8px;}
        .tkd-desc{font-size:.76rem;color:rgba(255,255,255,.72);line-height:1.55;max-width:300px;}
        .tkd-meta-chips{display:flex;gap:7px;flex-wrap:wrap;}
        .tkd-meta-chip{display:flex;align-items:center;gap:5px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.13);color:rgba(255,255,255,.72);font-size:.68rem;font-weight:600;padding:5px 10px;border-radius:8px;}
        .tkd-hero-right{position:relative;overflow:hidden;}
        .tkd-event-img{width:100%;height:100%;object-fit:cover;display:block;}
        .tkd-img-placeholder{width:100%;height:100%;min-height:230px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;position:relative;}
        .tkd-placeholder-bg{position:absolute;inset:0;}
        .tkd-placeholder-label{position:relative;z-index:1;font-size:.72rem;font-weight:600;color:rgba(255,255,255,.4);}
        .tkd-strip{background:#111827;display:flex;align-items:center;justify-content:space-between;padding:10px 24px;border-top:1px solid rgba(255,255,255,.06);border-bottom:1px solid rgba(255,255,255,.06);}
        .tkd-strip-code{font-family:'Courier New',monospace;font-size:.82rem;font-weight:700;color:#60A5FA;letter-spacing:.08em;}
        .tkd-strip-type{font-size:.65rem;font-weight:700;color:rgba(255,255,255,.52);text-transform:uppercase;letter-spacing:.1em;}
        .tkd-tear{background:#111827;display:flex;align-items:center;padding:0 14px;}
        .tkd-tear-circle{width:22px;height:22px;border-radius:50%;background:#0F172A;flex-shrink:0;margin:0 -11px;border:1px solid rgba(255,255,255,.06);}
        .tkd-tear-line{flex:1;border-top:2px dashed rgba(255,255,255,.07);margin:0 8px;}
        .tkd-details{background:#111827;padding:22px 24px;}
        .tkd-details-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px 14px;}
        .tkd-dl{font-size:.58rem;font-weight:700;color:rgba(255,255,255,.28);text-transform:uppercase;letter-spacing:.1em;margin-bottom:5px;}
        .tkd-dv{font-size:.82rem;font-weight:600;color:#E2E8F0;}
        .tkd-mono{font-family:'Courier New',monospace;font-size:.75rem;color:#60A5FA;}
        .tkd-badge{display:inline-flex;align-items:center;gap:5px;font-size:.65rem;font-weight:700;padding:3px 10px;border-radius:999px;border:1px solid;}
        .tkd-badge-green{background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.25);color:#4ade80;}
        .tkd-badge-gray{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.15);color:rgba(255,255,255,.5);}
        .tkd-badge-danger{background:rgba(220,38,38,.15);border-color:rgba(220,38,38,.3);color:#f87171;}
        .tkd-badge-dot{width:5px;height:5px;border-radius:50%;background:currentColor;}
        .tkd-qr-row{background:#111827;padding:16px 24px 20px;display:flex;align-items:center;gap:18px;border-top:1px solid rgba(255,255,255,.05);}
        .tkd-qr-box{width:128px;height:128px;background:#fff;border-radius:12px;padding:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;}
        .tkd-qr-box img{width:100%;height:100%;object-fit:contain;}
        .tkd-qr-fallback{font-size:.82rem;font-weight:700;color:#0f172a;}
        .tkd-qr-info{flex:1;}
        .tkd-qr-title{font-size:.78rem;font-weight:700;color:#E2E8F0;margin-bottom:4px;}
        .tkd-qr-sub{font-size:.68rem;color:rgba(255,255,255,.55);line-height:1.55;}
        .tkd-zone-box{background:rgba(37,99,235,.1);border:1px solid rgba(37,99,235,.25);border-radius:10px;padding:10px 16px;text-align:center;flex-shrink:0;}
        .tkd-zone-label{font-size:.58rem;font-weight:700;color:rgba(255,255,255,.3);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;}
        .tkd-zone-val{font-size:1rem;font-weight:700;color:#60A5FA;}
        .tkd-zone-sub{font-size:.62rem;color:rgba(255,255,255,.28);margin-top:2px;}
        .tkd-footer{background:#0D1321;padding:14px 24px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid rgba(255,255,255,.06);flex-wrap:wrap;gap:10px;}
        .tkd-footer-route{display:flex;align-items:center;gap:8px;font-size:.72rem;color:rgba(255,255,255,.38);}
        .tkd-footer-route strong{color:rgba(255,255,255,.72);font-weight:700;}
        .tkd-route-dot{width:7px;height:7px;border-radius:50%;background:#2563EB;flex-shrink:0;}
        .tkd-footer-actions{display:flex;gap:8px;flex-wrap:wrap;}
        .tkd-btn{display:inline-block;text-decoration:none;border-radius:9px;padding:8px 16px;font-size:.72rem;font-weight:700;font-family:inherit;cursor:pointer;border:none;transition:all .15s;}
        .tkd-btn-primary{background:#2563EB;color:#fff;}
        .tkd-btn-ghost{background:rgba(255,255,255,.06);color:rgba(255,255,255,.72);border:1px solid rgba(255,255,255,.12);}
        .tkd-btn-danger{background:#DC2626;color:#fff;}
        @media(max-width:600px){
            .tkd-hero{grid-template-columns:1fr;}
            .tkd-hero-right{min-height:160px;}
            .tkd-details-grid{grid-template-columns:repeat(2,1fr);}
            .tkd-footer{flex-direction:column;align-items:flex-start;}
            .tkd-qr-row{flex-wrap:wrap;}
        }
    </style>
</head>
<body>
    <div class="tkd-wrap">
        @include('pages.tickets.partials.card', ['ticket' => $ticket])
    </div>
</body>
</html>
