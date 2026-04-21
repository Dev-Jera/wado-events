<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your WADO Ticket</title>
    <style>
        /* Reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; display: block; }

        /* Responsive */
        @media only screen and (max-width: 620px) {
            .email-wrapper { padding: 12px 8px !important; }
            .email-card { border-radius: 12px !important; }
            .header-cell { padding: 20px 20px !important; }
            .header-title { font-size: 18px !important; }
            .hero-cell { padding: 22px 20px !important; }
            .hero-title { font-size: 22px !important; }
            .badges-table { width: 100% !important; }
            .badge-td { display: block !important; width: 100% !important; padding: 6px 0 !important; }
            .badge-spacer { display: none !important; }
            .badge-inner { display: block !important; width: 100% !important; box-sizing: border-box !important; text-align: left !important; }
            .details-cell { padding: 22px 20px !important; }
            .ticket-table { font-size: 13px !important; }
            .ticket-code { font-size: 16px !important; }
            .qr-img { width: 140px !important; height: 140px !important; }
            .cta-btn { padding: 13px 24px !important; font-size: 13px !important; }
            .note-cell { padding: 16px 20px !important; }
            .footer-cell { padding: 16px 20px !important; }
            .banner-img { height: 160px !important; object-fit: cover !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background:#f0f4fb;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="email-wrapper" style="background:#f0f4fb;padding:32px 16px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" border="0" class="email-card" style="max-width:600px;width:100%;border-radius:16px;overflow:hidden;">

                {{-- Header --}}
                <tr>
                    <td class="header-cell" style="background:#08111f;padding:24px 32px;text-align:center;">
                        <div class="header-title" style="font-size:22px;font-weight:900;color:#ffffff;letter-spacing:0.04em;">WADO TICKETS</div>
                        <div style="font-size:11px;color:rgba(255,255,255,0.5);margin-top:4px;letter-spacing:0.1em;text-transform:uppercase;">Your ticket is confirmed</div>
                    </td>
                </tr>

                {{-- Hero band --}}
                <tr>
                    <td style="height:5px;background:linear-gradient(90deg,#2563eb,#7c3aed,#dc2626);font-size:0;line-height:0;">&nbsp;</td>
                </tr>

                {{-- Event banner image --}}
                @if (!empty($eventImageUrl))
                <tr>
                    <td style="padding:0;line-height:0;font-size:0;">
                        <img src="{{ $eventImageUrl }}"
                             alt="{{ $ticket->event->title }}"
                             width="600"
                             class="banner-img"
                             style="width:100%;max-width:600px;height:200px;object-fit:cover;display:block;border:0;">
                    </td>
                </tr>
                @endif

                {{-- Event title block --}}
                <tr>
                    <td class="hero-cell" style="background:#172665;padding:28px 32px;">
                        <div style="font-size:11px;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:8px;">You're going to</div>
                        <div class="hero-title" style="font-size:26px;font-weight:800;color:#ffffff;line-height:1.2;margin-bottom:18px;">{{ $ticket->event?->title ?? 'WADO Event' }}</div>

                        {{-- Info badges — stack on mobile --}}
                        <table class="badges-table" cellpadding="0" cellspacing="0" border="0" style="border-collapse:separate;border-spacing:0;">
                            <tr>
                                {{-- Date --}}
                                <td class="badge-td" style="padding:0 8px 0 0;vertical-align:top;">
                                    <div class="badge-inner" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:10px 14px;">
                                        <table cellpadding="0" cellspacing="0" border="0"><tr>
                                            <td style="vertical-align:middle;padding-right:8px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                            </td>
                                            <td style="vertical-align:middle;">
                                                <div style="font-size:10px;color:rgba(255,255,255,0.45);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:2px;">Date</div>
                                                <div style="font-size:12px;font-weight:700;color:#ffffff;white-space:nowrap;">{{ $ticket->event?->starts_at?->format('d M Y') ?? 'TBD' }}</div>
                                            </td>
                                        </tr></table>
                                    </div>
                                </td>
                                {{-- Time --}}
                                <td class="badge-td" style="padding:0 8px 0 0;vertical-align:top;">
                                    <div class="badge-inner" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:10px 14px;">
                                        <table cellpadding="0" cellspacing="0" border="0"><tr>
                                            <td style="vertical-align:middle;padding-right:8px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            </td>
                                            <td style="vertical-align:middle;">
                                                <div style="font-size:10px;color:rgba(255,255,255,0.45);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:2px;">Time</div>
                                                <div style="font-size:12px;font-weight:700;color:#ffffff;white-space:nowrap;">{{ $ticket->event?->starts_at?->format('H:i') ?? 'TBD' }}</div>
                                            </td>
                                        </tr></table>
                                    </div>
                                </td>
                                {{-- Venue --}}
                                <td class="badge-td" style="padding:0;vertical-align:top;">
                                    <div class="badge-inner" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:10px 14px;">
                                        <table cellpadding="0" cellspacing="0" border="0"><tr>
                                            <td style="vertical-align:middle;padding-right:8px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            </td>
                                            <td style="vertical-align:middle;">
                                                <div style="font-size:10px;color:rgba(255,255,255,0.45);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:2px;">Venue</div>
                                                <div style="font-size:12px;font-weight:700;color:#ffffff;">{{ $ticket->event?->venue ?? '' }}{{ $ticket->event?->city ? ', ' . $ticket->event->city : '' }}</div>
                                            </td>
                                        </tr></table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Ticket details --}}
                <tr>
                    <td class="details-cell" style="background:#ffffff;padding:28px 32px;">

                        <table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:16px;"><tr>
                            <td style="vertical-align:middle;padding-right:7px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9l10-5 10 5v11a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            </td>
                            <td style="vertical-align:middle;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;">Ticket Details</td>
                        </tr></table>

                        <table class="ticket-table" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #e5e7eb;border-radius:12px;border-collapse:separate;border-spacing:0;overflow:hidden;">
                            <tr style="background:#f9fafb;">
                                <td style="padding:14px 18px;border-bottom:1px solid #e5e7eb;width:50%;">
                                    <div style="font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Ticket Code</div>
                                    <div class="ticket-code" style="font-size:17px;font-weight:800;color:#1e3799;font-family:monospace;word-break:break-all;">{{ $ticket->ticket_code }}</div>
                                </td>
                                <td style="padding:14px 18px;border-bottom:1px solid #e5e7eb;border-left:1px solid #e5e7eb;width:50%;">
                                    <div style="font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Holder</div>
                                    <div style="font-size:14px;font-weight:700;color:#111827;">{{ $ticket->holder_name ?: ($ticket->user?->name ?? 'N/A') }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:14px 18px;width:50%;">
                                    <div style="font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Category</div>
                                    <div style="font-size:14px;font-weight:700;color:#111827;">{{ $ticket->ticketCategory?->name ?? '—' }}</div>
                                </td>
                                <td style="padding:14px 18px;border-left:1px solid #e5e7eb;width:50%;">
                                    <div style="font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Quantity</div>
                                    <div style="font-size:14px;font-weight:700;color:#111827;">{{ $ticket->quantity }} ticket{{ $ticket->quantity > 1 ? 's' : '' }}</div>
                                </td>
                            </tr>
                        </table>

                        {{-- QR Code --}}
                        @if ($qrCodeDataUri)
                        <div style="text-align:center;margin:26px 0 18px;">
                            <table cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 14px;"><tr>
                                <td style="vertical-align:middle;padding-right:7px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><line x1="14" y1="14" x2="14" y2="21"/><line x1="14" y1="14" x2="21" y2="14"/><line x1="21" y1="18" x2="21" y2="21"/><line x1="18" y1="21" x2="21" y2="21"/></svg>
                                </td>
                                <td style="vertical-align:middle;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;">Your Entry QR Code</td>
                            </tr></table>
                            <div style="display:inline-block;background:#ffffff;border:2px solid #e5e7eb;border-radius:16px;padding:16px;">
                                <img class="qr-img" src="{{ $qrCodeDataUri }}" alt="Ticket QR Code" width="160" height="160" style="width:160px;height:160px;display:block;">
                            </div>
                            <div style="font-size:12px;color:#9ca3af;margin-top:10px;">Show this at the gate · Full PDF attached</div>
                        </div>
                        @endif

                        {{-- CTA Button --}}
                        @if ($ticketUrl)
                        <div style="text-align:center;margin:20px 0 4px;">
                            <a href="{{ $ticketUrl }}" class="cta-btn" style="display:inline-block;background:#08111f;color:#ffffff;font-weight:700;font-size:14px;padding:14px 32px;border-radius:10px;text-decoration:none;letter-spacing:0.02em;">View My Ticket Online</a>
                        </div>
                        @endif

                    </td>
                </tr>

                {{-- Note --}}
                <tr>
                    <td class="note-cell" style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:20px 32px;">
                        <table cellpadding="0" cellspacing="0" border="0"><tr>
                            <td style="vertical-align:top;padding-right:10px;padding-top:2px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            </td>
                            <td style="vertical-align:top;font-size:12px;color:#6b7280;line-height:1.7;">
                                <strong style="color:#374151;">What to bring:</strong> Present your QR code at the gate — from this email, the attached PDF, or your ticket page online. Keep your code <strong style="color:#374151;">{{ $ticket->ticket_code }}</strong> handy as backup.
                            </td>
                        </tr></table>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td class="footer-cell" style="background:#08111f;padding:20px 32px;text-align:center;">
                        <div style="font-size:12px;color:rgba(255,255,255,0.4);">© {{ date('Y') }} WADO Tickets &nbsp;·&nbsp; Your ticket PDF is attached</div>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
