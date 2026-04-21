<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your WADO Ticket</title>
</head>
<body style="margin:0;padding:0;background:#f0f4fb;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4fb;padding:32px 16px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

                {{-- Header --}}
                <tr>
                    <td style="background:#08111f;border-radius:16px 16px 0 0;padding:24px 32px;text-align:center;">
                        <div style="font-size:22px;font-weight:900;color:#ffffff;letter-spacing:0.04em;">WADO TICKETS</div>
                        <div style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:4px;letter-spacing:0.1em;text-transform:uppercase;">Your ticket is confirmed</div>
                    </td>
                </tr>

                {{-- Hero band --}}
                <tr>
                    <td style="height:5px;background:linear-gradient(90deg,#2563eb,#7c3aed,#dc2626);"></td>
                </tr>

                {{-- Event title block --}}
                <tr>
                    <td style="background:#172665;padding:28px 32px;">
                        <div style="font-size:11px;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:8px;">You're going to</div>
                        <div style="font-size:26px;font-weight:800;color:#ffffff;line-height:1.2;margin-bottom:16px;">{{ $ticket->event?->title ?? 'WADO Event' }}</div>
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:7px 14px;font-size:12px;font-weight:700;color:rgba(255,255,255,0.9);margin-right:8px;">
                                    📅 {{ $ticket->event?->starts_at?->format('d M Y') ?? 'TBD' }}
                                </td>
                                <td width="8"></td>
                                <td style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:7px 14px;font-size:12px;font-weight:700;color:rgba(255,255,255,0.9);">
                                    🕐 {{ $ticket->event?->starts_at?->format('H:i') ?? 'TBD' }}
                                </td>
                                <td width="8"></td>
                                <td style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);border-radius:8px;padding:7px 14px;font-size:12px;font-weight:700;color:rgba(255,255,255,0.9);">
                                    📍 {{ $ticket->event?->venue ?? '' }}{{ $ticket->event?->city ? ', ' . $ticket->event->city : '' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Ticket details --}}
                <tr>
                    <td style="background:#ffffff;padding:28px 32px;">

                        <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:16px;">Ticket Details</div>

                        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                            <tr style="background:#f9fafb;">
                                <td style="padding:14px 20px;border-bottom:1px solid #e5e7eb;">
                                    <div style="font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Ticket Code</div>
                                    <div style="font-size:18px;font-weight:800;color:#1e3799;font-family:monospace;">{{ $ticket->ticket_code }}</div>
                                </td>
                                <td style="padding:14px 20px;border-bottom:1px solid #e5e7eb;border-left:1px solid #e5e7eb;">
                                    <div style="font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Holder</div>
                                    <div style="font-size:14px;font-weight:700;color:#111827;">{{ $ticket->holder_name ?: ($ticket->user?->name ?? 'N/A') }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:14px 20px;">
                                    <div style="font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Category</div>
                                    <div style="font-size:14px;font-weight:700;color:#111827;">{{ $ticket->ticketCategory?->name ?? '—' }}</div>
                                </td>
                                <td style="padding:14px 20px;border-left:1px solid #e5e7eb;">
                                    <div style="font-size:11px;color:#9ca3af;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Quantity</div>
                                    <div style="font-size:14px;font-weight:700;color:#111827;">{{ $ticket->quantity }} ticket{{ $ticket->quantity > 1 ? 's' : '' }}</div>
                                </td>
                            </tr>
                        </table>

                        {{-- QR Code --}}
                        @if ($qrCodeDataUri)
                        <div style="text-align:center;margin:24px 0 16px;">
                            <div style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:12px;">Your Entry QR Code</div>
                            <div style="display:inline-block;background:#ffffff;border:2px solid #e5e7eb;border-radius:16px;padding:16px;">
                                <img src="{{ $qrCodeDataUri }}" alt="Ticket QR Code" style="width:160px;height:160px;display:block;">
                            </div>
                            <div style="font-size:12px;color:#9ca3af;margin-top:10px;">Screenshot or print — your full ticket PDF is attached</div>
                        </div>
                        @endif

                        {{-- CTA Button --}}
                        @if ($ticketUrl)
                        <div style="text-align:center;margin:24px 0 8px;">
                            <a href="{{ $ticketUrl }}" style="display:inline-block;background:#08111f;color:#ffffff;font-weight:700;font-size:14px;padding:14px 32px;border-radius:10px;text-decoration:none;letter-spacing:0.02em;">View My Ticket Online</a>
                        </div>
                        @endif

                    </td>
                </tr>

                {{-- Note --}}
                <tr>
                    <td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:20px 32px;">
                        <div style="font-size:12px;color:#6b7280;line-height:1.6;">
                            <strong style="color:#374151;">What to bring:</strong> Present your QR code at the gate — either from this email, the attached PDF, or your ticket page. Keep your ticket code <strong style="color:#374151;">{{ $ticket->ticket_code }}</strong> handy as a backup.
                        </div>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="background:#08111f;border-radius:0 0 16px 16px;padding:20px 32px;text-align:center;">
                        <div style="font-size:12px;color:rgba(255,255,255,0.4);">© {{ date('Y') }} WADO Tickets · Your ticket PDF is attached to this email</div>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
