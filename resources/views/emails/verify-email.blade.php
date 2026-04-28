<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify your email — WADO Events</title>
</head>
<body style="margin:0;padding:0;background:#f7f3f3;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f7f3f3;padding:40px 16px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        {{-- Brand --}}
        <tr>
          <td style="padding-bottom:20px;text-align:center;">
            <span style="font-size:1.1rem;font-weight:800;letter-spacing:.06em;color:#c0283c;text-transform:uppercase;">WADO Events</span>
          </td>
        </tr>

        {{-- Card --}}
        <tr>
          <td style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,.08);">

            {{-- Header --}}
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="background:linear-gradient(135deg,#c0283c 0%,#8a1525 100%);padding:36px 40px;text-align:center;">
                  <div style="width:56px;height:56px;background:rgba(255,255,255,.15);border-radius:50%;margin:0 auto 16px;display:inline-flex;align-items:center;justify-content:center;">
                    <span style="font-size:1.6rem;">✉️</span>
                  </div>
                  <h1 style="margin:0;font-size:1.45rem;font-weight:800;color:#ffffff;line-height:1.2;">Confirm your email</h1>
                  <p style="margin:10px 0 0;font-size:.88rem;color:rgba(255,255,255,.78);">One quick step to secure your WADO account</p>
                </td>
              </tr>
            </table>

            {{-- Body --}}
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:32px 40px 8px;">
                  <p style="margin:0 0 8px;font-size:.95rem;color:#3a1015;">Hi <strong>{{ $user->name }}</strong>,</p>
                  <p style="margin:0 0 24px;font-size:.9rem;color:#6b3040;line-height:1.6;">
                    Thanks for creating an account. Please verify your email address so we can deliver your tickets and payment confirmations without a hitch.
                  </p>

                  {{-- CTA button --}}
                  <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
                    <tr>
                      <td align="center">
                        <a href="{{ $verifyUrl }}"
                           style="display:inline-block;background:#c0283c;color:#ffffff;font-size:.92rem;font-weight:700;text-decoration:none;padding:15px 40px;border-radius:10px;letter-spacing:.02em;">
                          Verify my email
                        </a>
                      </td>
                    </tr>
                  </table>

                  <p style="margin:0 0 6px;font-size:.8rem;color:#b08090;text-align:center;">This link expires in <strong>60 minutes</strong>.</p>
                  <p style="margin:0 0 24px;font-size:.78rem;color:#b08090;text-align:center;">
                    If the button doesn't work, copy and paste this link into your browser:
                  </p>
                  <div style="background:#fdf8f8;border:1px solid #f0dde0;border-radius:8px;padding:10px 14px;margin-bottom:28px;word-break:break-all;">
                    <a href="{{ $verifyUrl }}" style="font-size:.72rem;color:#c0283c;text-decoration:none;">{{ $verifyUrl }}</a>
                  </div>

                  <p style="margin:0 0 28px;font-size:.8rem;color:#b08090;">
                    If you didn't create a WADO Events account, you can safely ignore this email.
                  </p>
                </td>
              </tr>
            </table>

          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="padding:24px 0 8px;text-align:center;">
            <p style="margin:0;font-size:.75rem;color:#b08090;">WADO Events · Tickets, managed.</p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
