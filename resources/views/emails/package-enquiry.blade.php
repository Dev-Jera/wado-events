<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Package Enquiry</title>
</head>
<body style="margin:0;padding:0;background:#f7f3f3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f7f3f3;padding:40px 16px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        {{-- Logo / brand bar --}}
        <tr>
          <td style="padding-bottom:20px;text-align:center;">
            <span style="font-size:1.1rem;font-weight:800;letter-spacing:.06em;color:#c0283c;text-transform:uppercase;">WADO Events</span>
          </td>
        </tr>

        {{-- Card --}}
        <tr>
          <td style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.08);">

            {{-- Header stripe --}}
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="background:linear-gradient(135deg,#c0283c 0%,#8a1525 100%);padding:36px 40px;">
                  <p style="margin:0 0 6px;font-size:.72rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.65);">New Enquiry Received</p>
                  <h1 style="margin:0;font-size:1.5rem;font-weight:800;color:#ffffff;line-height:1.2;">Package Enquiry</h1>
                  <p style="margin:10px 0 0;font-size:.85rem;color:rgba(255,255,255,.75);">Someone is interested in your services.</p>
                </td>
              </tr>
            </table>

            {{-- Package badge --}}
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:28px 40px 0;">
                  <span style="display:inline-block;background:#fdf2f3;border:1.5px solid #f0c5cc;color:#c0283c;font-size:.72rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;border-radius:999px;padding:5px 16px;">
                    {{ $data['package'] }}
                  </span>
                </td>
              </tr>
            </table>

            {{-- Contact details --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="padding:24px 40px 0;">
              <tr>
                <td style="padding:0 0 20px;">
                  <p style="margin:0 0 14px;font-size:.68rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#b08090;">Contact Details</p>

                  {{-- Name --}}
                  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
                    <tr>
                      <td width="140" style="font-size:.75rem;font-weight:700;color:#9a5060;padding-top:2px;vertical-align:top;">Name</td>
                      <td style="font-size:.95rem;font-weight:600;color:#1a0508;">{{ $data['name'] }}</td>
                    </tr>
                  </table>

                  {{-- Email --}}
                  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
                    <tr>
                      <td width="140" style="font-size:.75rem;font-weight:700;color:#9a5060;padding-top:2px;vertical-align:top;">Email</td>
                      <td style="font-size:.95rem;color:#1a0508;">
                        <a href="mailto:{{ $data['email'] }}" style="color:#c0283c;text-decoration:none;font-weight:600;">{{ $data['email'] }}</a>
                      </td>
                    </tr>
                  </table>

                  @if (!empty($data['phone']))
                  {{-- Phone --}}
                  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
                    <tr>
                      <td width="140" style="font-size:.75rem;font-weight:700;color:#9a5060;padding-top:2px;vertical-align:top;">Phone</td>
                      <td style="font-size:.95rem;color:#1a0508;">
                        <a href="tel:{{ $data['phone'] }}" style="color:#1a0508;text-decoration:none;">{{ $data['phone'] }}</a>
                      </td>
                    </tr>
                  </table>
                  @endif
                </td>
              </tr>
            </table>

            @if (!empty($data['event_date']) || !empty($data['attendance']))
            {{-- Divider --}}
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:0 40px;">
                  <div style="border-top:1px solid #f0dde0;"></div>
                </td>
              </tr>
            </table>

            {{-- Event details --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="padding:20px 40px 0;">
              <tr>
                <td style="padding-bottom:20px;">
                  <p style="margin:0 0 14px;font-size:.68rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#b08090;">Event Details</p>

                  @if (!empty($data['event_date']))
                  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
                    <tr>
                      <td width="140" style="font-size:.75rem;font-weight:700;color:#9a5060;padding-top:2px;vertical-align:top;">Event Date</td>
                      <td style="font-size:.95rem;font-weight:600;color:#1a0508;">{{ \Carbon\Carbon::parse($data['event_date'])->format('d F Y') }}</td>
                    </tr>
                  </table>
                  @endif

                  @if (!empty($data['attendance']))
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="140" style="font-size:.75rem;font-weight:700;color:#9a5060;padding-top:2px;vertical-align:top;">Attendance</td>
                      <td style="font-size:.95rem;font-weight:600;color:#1a0508;">{{ $data['attendance'] }} guests</td>
                    </tr>
                  </table>
                  @endif
                </td>
              </tr>
            </table>
            @endif

            @if (!empty($data['message']))
            {{-- Divider --}}
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:0 40px;">
                  <div style="border-top:1px solid #f0dde0;"></div>
                </td>
              </tr>
            </table>

            {{-- Message --}}
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:20px 40px;">
                  <p style="margin:0 0 10px;font-size:.68rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#b08090;">Additional Details</p>
                  <div style="background:#fdf8f8;border-left:3px solid #c0283c;border-radius:0 8px 8px 0;padding:14px 18px;">
                    <p style="margin:0;font-size:.92rem;color:#3a1015;line-height:1.7;white-space:pre-wrap;">{{ $data['message'] }}</p>
                  </div>
                </td>
              </tr>
            </table>
            @endif

            {{-- CTA --}}
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:4px 40px 36px;text-align:center;">
                  <a href="mailto:{{ $data['email'] }}?subject=Re: Your enquiry about {{ rawurlencode($data['package']) }}"
                     style="display:inline-block;background:#c0283c;color:#ffffff;font-size:.85rem;font-weight:700;text-decoration:none;padding:13px 32px;border-radius:10px;letter-spacing:.02em;">
                    Reply to {{ $data['name'] }}
                  </a>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="padding:24px 0 8px;text-align:center;">
            <p style="margin:0;font-size:.75rem;color:#b08090;">This notification was sent to the WADO Events team.</p>
            <p style="margin:6px 0 0;font-size:.75rem;color:#c0a0a8;">WADO Events &middot; wado-events-production.up.railway.app</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
