<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>WADO Events Tickets — API & Endpoint Reference</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
                    body .content .php-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost";
        var useCsrf = Boolean(1);
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.9.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.9.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;,&quot;php&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                            <button type="button" class="lang-button" data-language-name="php">php</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authentication" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authentication">
                    <a href="#authentication">Authentication</a>
                </li>
                                    <ul id="tocify-subheader-authentication" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="authentication-GETlogin">
                                <a href="#authentication-GETlogin">GET login</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="authentication-POSTlogin">
                                <a href="#authentication-POSTlogin">POST login</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="authentication-GETregister">
                                <a href="#authentication-GETregister">GET register</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="authentication-POSTregister">
                                <a href="#authentication-POSTregister">POST register</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="authentication-POSTlogout">
                                <a href="#authentication-POSTlogout">POST logout</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-events" class="tocify-header">
                <li class="tocify-item level-1" data-unique="events">
                    <a href="#events">Events</a>
                </li>
                                    <ul id="tocify-subheader-events" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="events-GETevents">
                                <a href="#events-GETevents">GET events</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="events-GETevents--id-">
                                <a href="#events-GETevents--id-">GET events/{id}</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-checkout" class="tocify-header">
                <li class="tocify-item level-1" data-unique="checkout">
                    <a href="#checkout">Checkout</a>
                </li>
                                    <ul id="tocify-subheader-checkout" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="checkout-GETevents--event_id--checkout">
                                <a href="#checkout-GETevents--event_id--checkout">GET events/{event_id}/checkout</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="checkout-POSTevents--event_id--checkout">
                                <a href="#checkout-POSTevents--event_id--checkout">POST events/{event_id}/checkout</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-tickets" class="tocify-header">
                <li class="tocify-item level-1" data-unique="tickets">
                    <a href="#tickets">Tickets</a>
                </li>
                                    <ul id="tocify-subheader-tickets" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="tickets-GETmy-tickets">
                                <a href="#tickets-GETmy-tickets">GET my-tickets</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="tickets-GETmy-tickets--id-">
                                <a href="#tickets-GETmy-tickets--id-">GET my-tickets/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="tickets-GETmy-tickets--ticket_id--download">
                                <a href="#tickets-GETmy-tickets--ticket_id--download">GET my-tickets/{ticket_id}/download</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="tickets-GETmy-tickets--ticket_id--pdf">
                                <a href="#tickets-GETmy-tickets--ticket_id--pdf">GET my-tickets/{ticket_id}/pdf</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="tickets-POSTmy-tickets--ticket_id--refund-request">
                                <a href="#tickets-POSTmy-tickets--ticket_id--refund-request">POST my-tickets/{ticket_id}/refund-request</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-gate-portal" class="tocify-header">
                <li class="tocify-item level-1" data-unique="gate-portal">
                    <a href="#gate-portal">Gate Portal</a>
                </li>
                                    <ul id="tocify-subheader-gate-portal" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="gate-portal-GETgate-portal">
                                <a href="#gate-portal-GETgate-portal">GET gate-portal</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="gate-portal-POSTgate-portal-walk-in-sale">
                                <a href="#gate-portal-POSTgate-portal-walk-in-sale">POST gate-portal/walk-in-sale</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-ticket-verification" class="tocify-header">
                <li class="tocify-item level-1" data-unique="ticket-verification">
                    <a href="#ticket-verification">Ticket Verification</a>
                </li>
                                    <ul id="tocify-subheader-ticket-verification" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="ticket-verification-GETticket-verification">
                                <a href="#ticket-verification-GETticket-verification">GET ticket-verification</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="ticket-verification-POSTticket-verification">
                                <a href="#ticket-verification-POSTticket-verification">POST ticket-verification</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="ticket-verification-POSTticket-verification-scan">
                                <a href="#ticket-verification-POSTticket-verification-scan">POST ticket-verification/scan</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="ticket-verification-GETticket-verification-export">
                                <a href="#ticket-verification-GETticket-verification-export">GET ticket-verification/export</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-admin-payments" class="tocify-header">
                <li class="tocify-item level-1" data-unique="admin-payments">
                    <a href="#admin-payments">Admin — Payments</a>
                </li>
                                    <ul id="tocify-subheader-admin-payments" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="admin-payments-GETadmin-payments">
                                <a href="#admin-payments-GETadmin-payments">GET admin/payments</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="admin-payments-POSTadmin-payments--paymentTransaction_id--resend">
                                <a href="#admin-payments-POSTadmin-payments--paymentTransaction_id--resend">POST admin/payments/{paymentTransaction_id}/resend</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="admin-payments-POSTadmin-payments--paymentTransaction_id--confirm">
                                <a href="#admin-payments-POSTadmin-payments--paymentTransaction_id--confirm">POST admin/payments/{paymentTransaction_id}/confirm</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="admin-payments-POSTadmin-payments--paymentTransaction_id--refund">
                                <a href="#admin-payments-POSTadmin-payments--paymentTransaction_id--refund">POST admin/payments/{paymentTransaction_id}/refund</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-webhooks" class="tocify-header">
                <li class="tocify-item level-1" data-unique="webhooks">
                    <a href="#webhooks">Webhooks</a>
                </li>
                                    <ul id="tocify-subheader-webhooks" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="webhooks-POSTpayments-marzepay-webhook">
                                <a href="#webhooks-POSTpayments-marzepay-webhook">POST payments/marzepay/webhook</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-general" class="tocify-header">
                <li class="tocify-item level-1" data-unique="general">
                    <a href="#general">General</a>
                </li>
                                    <ul id="tocify-subheader-general" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="general-GETfilament-exports--export_id--download">
                                <a href="#general-GETfilament-exports--export_id--download">GET filament/exports/{export_id}/download</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETfilament-imports--import_id--failed-rows-download">
                                <a href="#general-GETfilament-imports--import_id--failed-rows-download">GET filament/imports/{import_id}/failed-rows/download</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETdashboard">
                                <a href="#general-GETdashboard">GET dashboard</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-POSTlivewire-dbf87c49-update">
                                <a href="#general-POSTlivewire-dbf87c49-update">POST livewire-dbf87c49/update</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETlivewire-dbf87c49-livewire-js">
                                <a href="#general-GETlivewire-dbf87c49-livewire-js">GET livewire-dbf87c49/livewire.js</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETlivewire-dbf87c49-livewire-min-js-map">
                                <a href="#general-GETlivewire-dbf87c49-livewire-min-js-map">GET livewire-dbf87c49/livewire.min.js.map</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETlivewire-dbf87c49-livewire-csp-min-js-map">
                                <a href="#general-GETlivewire-dbf87c49-livewire-csp-min-js-map">GET livewire-dbf87c49/livewire.csp.min.js.map</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-POSTlivewire-dbf87c49-upload-file">
                                <a href="#general-POSTlivewire-dbf87c49-upload-file">POST livewire-dbf87c49/upload-file</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETlivewire-dbf87c49-preview-file--filename-">
                                <a href="#general-GETlivewire-dbf87c49-preview-file--filename-">GET livewire-dbf87c49/preview-file/{filename}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETlivewire-dbf87c49-js--component--js">
                                <a href="#general-GETlivewire-dbf87c49-js--component--js">GET livewire-dbf87c49/js/{component}.js</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETlivewire-dbf87c49-css--component--css">
                                <a href="#general-GETlivewire-dbf87c49-css--component--css">GET livewire-dbf87c49/css/{component}.css</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETlivewire-dbf87c49-css--component--global-css">
                                <a href="#general-GETlivewire-dbf87c49-css--component--global-css">GET livewire-dbf87c49/css/{component}.global.css</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-POSTresend-webhook">
                                <a href="#general-POSTresend-webhook">Handle a Resend webhook call.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETup">
                                <a href="#general-GETup">GET up</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GEThealth">
                                <a href="#general-GEThealth">GET health</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GET-">
                                <a href="#general-GET-">GET /</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-POSTmy-tickets--ticket_id--dismiss">
                                <a href="#general-POSTmy-tickets--ticket_id--dismiss">POST my-tickets/{ticket_id}/dismiss</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-POSTevents--event_id--bookmark">
                                <a href="#general-POSTevents--event_id--bookmark">POST events/{event_id}/bookmark</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETadmin-events--id-">
                                <a href="#general-GETadmin-events--id-">GET admin/events/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETadmin-finance">
                                <a href="#general-GETadmin-finance">GET admin/finance</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETadmin-finance--event_id-">
                                <a href="#general-GETadmin-finance--event_id-">GET admin/finance/{event_id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETstorage--path-">
                                <a href="#general-GETstorage--path-">GET storage/{path}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-PUTstorage--path-">
                                <a href="#general-PUTstorage--path-">PUT storage/{path}</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: April 23, 2026 (512973f)</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>Complete reference for all WADO Events Tickets endpoints: authentication, event browsing, ticket checkout, gate scanning, walk-in sales, payment webhooks, and admin operations.</p>
<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>
<pre><code>This documentation covers every HTTP endpoint in the WADO Events Tickets platform.

**Authentication:** Most endpoints require an active browser session (cookie-based). Log in at `/login` first, then use the Try It Out buttons — your session carries over automatically.

**Webhook endpoints** (e.g. MarzPay) are called by external payment providers, not by the browser.

**Admin endpoints** require the `super_admin` or `admin` role.

**Gate portal / scan endpoints** require the `gate_agent` role assigned to the relevant event.</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>To authenticate requests, include a <strong><code>Cookie</code></strong> header with the value <strong><code>"{YOUR_SESSION_COOKIE}"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>This API uses session-based authentication. Log in at <a href="/login">/login</a> first — your browser session cookie is sent automatically when using Try It Out.</p>

        <h1 id="authentication">Authentication</h1>

    <p>Login, registration, and logout.</p>

                                <h2 id="authentication-GETlogin">GET login</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETlogin">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/login" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/login"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/login';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETlogin">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IkxNRmQrZk95a2dsd3NCUUdCRlZHMUE9PSIsInZhbHVlIjoiRit1M3lOVzNsV2k0SGRZTjZIRElVTVREY1QwZGsralZXZE5ObUpiOGVma1BHd3h6SVlTdnlJWU1LcDRNOVUxcFRqcDNTSkZuSDR1d05LWmFnVkJTV1A2dlRsTjNENmwzZWZSdTZjWnFJR21jbEs3eTJmUmNWNlhWMnliWXFYK1YiLCJtYWMiOiI4YWRiY2I2MmQ1YWVhNGM1NDVmZmY2MmI1NTllYjE4YTVlYjk4ZWRkODg4MWUwMDk4ODBjNzc1OWQyYmQ2Nzg2IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6Im1tSDMrNWJtdFBiZHlsT1VSVlg5Ymc9PSIsInZhbHVlIjoidk9uL2kxMVVzVXVBSW1vNitNWlNjczZ6ZGxmaWwvekVoNHU1NUhhUHVBMFBzNENEd21xeExWQms1QzVNNU12UlVZbzF5WFVPcElNQXlVT3BGb0hwM2VQOFFmTEpNN0o0Y0gvMU55aWhMd2hoV2FrWU9ZWDl5UVArTkRBaCtQbkMiLCJtYWMiOiJmMDEwMGZhZTYwMDEyNDFiNDg2OGExYmM2M2U0NmFmNzdmNzIzNDNhN2RmZTRlOGQ4NGFjMGU2NWEwZTRlZTdhIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETlogin" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETlogin"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETlogin"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETlogin" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETlogin">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETlogin" data-method="GET"
      data-path="login"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETlogin', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETlogin"
                    onclick="tryItOut('GETlogin');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETlogin"
                    onclick="cancelTryOut('GETlogin');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETlogin"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETlogin"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETlogin"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETlogin"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="authentication-POSTlogin">POST login</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTlogin">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/login" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json" \
    --data "{
    \"email\": \"qkunze@example.com\",
    \"password\": \"O[2UZ5ij-e\\/dl4m{o,\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/login"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};

let body = {
    "email": "qkunze@example.com",
    "password": "O[2UZ5ij-e\/dl4m{o,"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/login';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'email' =&gt; 'qkunze@example.com',
            'password' =&gt; 'O[2UZ5ij-e/dl4m{o,',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTlogin">
</span>
<span id="execution-results-POSTlogin" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTlogin"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTlogin"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTlogin" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTlogin">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTlogin" data-method="POST"
      data-path="login"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTlogin', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTlogin"
                    onclick="tryItOut('POSTlogin');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTlogin"
                    onclick="cancelTryOut('POSTlogin');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTlogin"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTlogin"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTlogin"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTlogin"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTlogin"
               value="qkunze@example.com"
               data-component="body">
    <br>
<p>Must be a valid email address. Example: <code>qkunze@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTlogin"
               value="O[2UZ5ij-e/dl4m{o,"
               data-component="body">
    <br>
<p>Example: <code>O[2UZ5ij-e/dl4m{o,</code></p>
        </div>
        </form>

                    <h2 id="authentication-GETregister">GET register</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETregister">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/register" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/register"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/register';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETregister">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IkxwREpqNUQyWE9JZFl0akhKSTNpekE9PSIsInZhbHVlIjoicU9sVTJNck5SdUJPTmovM29idmw1aWJKQ3daS2Q3Vnl5a1MrajQ0eVNsN2FYMHVQbUVBNlhuaDN1WWVlQXhjeGdiYlZZL3ljUnIwaWx0N0RzMVdDMHdJYlJDKzI5elVZTUljYjNNWC82Skt5dVpUUy8yZTlpaWVkdmlrMkRIY20iLCJtYWMiOiI4OTBhNTcxYTZhYzdkNDg5NzViZWY5MWJmOGFkNDgwYWRiM2Y2MzBjNjU5NGM0YWE3NmE5NDRlZjM0NmFmOTAxIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6IjFUVGN0MVE0QWYzR2RCVE55K1k1T3c9PSIsInZhbHVlIjoiQnJicVhxVWtyTU5qd0hOZFZuazZ5K3hVUld1R0xmU2dYT2JMK0I0S29yeVUxdjdhVU5nTmdJK1MvU2VHN1JsR0NZYXo0RWtzL3JnamthTEE0TFJTeXRtVis4M3dNQjUxSzFaWVZPcmJnbzdkSUxIV0IvWkM5WkFGY1d2MnFmN0IiLCJtYWMiOiIxOGMwNWI0ZjU0ODQwMmNiZDA3ODdhMDc1ZTY1NGNlNDczYzI0ZDNkYjA2YTY0NDMyNGRlOTRjNTM4NTQ4NzU3IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETregister" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETregister"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETregister"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETregister" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETregister">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETregister" data-method="GET"
      data-path="register"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETregister', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETregister"
                    onclick="tryItOut('GETregister');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETregister"
                    onclick="cancelTryOut('GETregister');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETregister"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>register</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETregister"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETregister"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETregister"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="authentication-POSTregister">POST register</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTregister">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/register" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json" \
    --data "{
    \"name\": \"vmqeopfuudtdsufvyvddq\",
    \"email\": \"kunde.eloisa@example.com\",
    \"phone\": \"hfqcoynlazghdtqtqxbaj\",
    \"password\": \"\'YAKYLk4&gt;SJIrIV#lz.\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/register"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};

let body = {
    "name": "vmqeopfuudtdsufvyvddq",
    "email": "kunde.eloisa@example.com",
    "phone": "hfqcoynlazghdtqtqxbaj",
    "password": "'YAKYLk4&gt;SJIrIV#lz."
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/register';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'name' =&gt; 'vmqeopfuudtdsufvyvddq',
            'email' =&gt; 'kunde.eloisa@example.com',
            'phone' =&gt; 'hfqcoynlazghdtqtqxbaj',
            'password' =&gt; '\'YAKYLk4&gt;SJIrIV#lz.',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTregister">
</span>
<span id="execution-results-POSTregister" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTregister"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTregister"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTregister" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTregister">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTregister" data-method="POST"
      data-path="register"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTregister', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTregister"
                    onclick="tryItOut('POSTregister');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTregister"
                    onclick="cancelTryOut('POSTregister');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTregister"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>register</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTregister"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTregister"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTregister"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTregister"
               value="vmqeopfuudtdsufvyvddq"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>vmqeopfuudtdsufvyvddq</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTregister"
               value="kunde.eloisa@example.com"
               data-component="body">
    <br>
<p>Must be a valid email address. Must not be greater than 255 characters. Example: <code>kunde.eloisa@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone"                data-endpoint="POSTregister"
               value="hfqcoynlazghdtqtqxbaj"
               data-component="body">
    <br>
<p>Must not be greater than 30 characters. Example: <code>hfqcoynlazghdtqtqxbaj</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTregister"
               value="'YAKYLk4>SJIrIV#lz."
               data-component="body">
    <br>
<p>Must be at least 8 characters. Example: <code>'YAKYLk4&gt;SJIrIV#lz.</code></p>
        </div>
        </form>

                    <h2 id="authentication-POSTlogout">POST logout</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTlogout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/logout" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/logout"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/logout';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTlogout">
</span>
<span id="execution-results-POSTlogout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTlogout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTlogout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTlogout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTlogout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTlogout" data-method="POST"
      data-path="logout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTlogout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTlogout"
                    onclick="tryItOut('POSTlogout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTlogout"
                    onclick="cancelTryOut('POSTlogout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTlogout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>logout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTlogout"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTlogout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTlogout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="events">Events</h1>

    <p>Browse and view public events.</p>

                                <h2 id="events-GETevents">GET events</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETevents">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/events" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/events"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/events';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETevents">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6Im9aL1NrWnJ3a0NZSDhuTmExMWJ5Z3c9PSIsInZhbHVlIjoiSDB1blpUTXZXMy9valBrcDNUZW1PSzlxazJCVTVWZmxhOWVSVFNMdDIxZDlYQ0hnN2tTbWNXVUlweUVEcGVyL3hpeG81OSs0M01WUUlKaFRqMUtDOU9FYmNzRXZLYWI1T3NmbU41blBobzhTV2RQRTBYdndONTh1OTNPNHkvaGciLCJtYWMiOiI2MzQ4MWMwMDFlNzU1NzU0MDAwZDg2YzdiY2RkMjViYjdlODJjOTRlZmIwZmU1YTE1YmY1MWYzYWQ4OWU4MTNkIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6IjVldVgzUXo0M2ZGOW5RN3FjeEw5Q1E9PSIsInZhbHVlIjoiS1NIYmtlbTNkWWQxRzZuT3QvWFY5UTNPYUt1eWdaT3RGdC9MNWtXZGV1Y0JJTENod3k5L3hzSzB4M09SdmtESTVndENmektJSFBwUnNMeWhla21MU1JVZ09tc3ZxSGRHQXhlY2EzWTRVYUF3V2lmcWtuUHh2U2dYQTdFVmRTMy8iLCJtYWMiOiJlYjM0MDk3ZmE2OTJlMDVhOWIwNjliODBlNzgzN2E1N2M4OWI2ZmFkOGJhMmVkYjlmMTczNWFlOGIzNTgxMjk5IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETevents" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETevents"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETevents"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETevents" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETevents">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETevents" data-method="GET"
      data-path="events"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETevents', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETevents"
                    onclick="tryItOut('GETevents');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETevents"
                    onclick="cancelTryOut('GETevents');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETevents"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>events</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETevents"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETevents"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETevents"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="events-GETevents--id-">GET events/{id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETevents--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/events/1" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/events/1"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/events/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETevents--id-">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IldIWllJNmZUWC9odkJpd1NzcDdKYkE9PSIsInZhbHVlIjoibjZNS0hwcU04WVdDWUtyVXdZVis1TUJGZG1yajhGVC8vclRWS2xtdUFDKy9HQWZhc0lKWFlvdFpDL0ZZMzlPdEtoR3drL3MvYllKWEltN2w1OVdCNkU2UTdmQnBlRHhYdmpYdWxRQTBrQ21rbk45NjlIT0s5b2QwMUFOWUs1Y2IiLCJtYWMiOiJmMzg0ZTU3ODFkMmY1ZTY1YTU0N2NlNzUyZmRjMTBkYTkyYjE1YjM2NWY3Y2UyYWU5MTFlOTQ5MzdmYmJhZjIyIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6IkpBampQcldSTWxtOVc1R3hhVVZYZHc9PSIsInZhbHVlIjoiZUNaQ1pzaEVrd0tpWjhBRTdNSkV5Y084YU5KSitCN05TVXc5QmMyMXYxOVdWSlFScE5YQW52WnUwYzNqNlhuWU0vM1NxSk1vY0JqbE5zL0g2d0pnRXBybWdRYzA1K3YzSUluNmtnMlpyTStaNnpENzYwbE56Vm5YeEZ1Tkt6b3giLCJtYWMiOiIzN2EwNGRlZDg5MWJlOWJmZGZmY2IwZDdjZTMxZGQ2YzUzZmI1NTk1MDUzZWEwODYyYTFlMmYzZDczNzNjZDdkIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETevents--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETevents--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETevents--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETevents--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETevents--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETevents--id-" data-method="GET"
      data-path="events/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETevents--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETevents--id-"
                    onclick="tryItOut('GETevents--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETevents--id-"
                    onclick="cancelTryOut('GETevents--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETevents--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>events/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETevents--id-"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETevents--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETevents--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETevents--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the event. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="checkout">Checkout</h1>

    <p>Seat reservation and ticket purchase flow.</p>

                                <h2 id="checkout-GETevents--event_id--checkout">GET events/{event_id}/checkout</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETevents--event_id--checkout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/events/1/checkout" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/events/1/checkout"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/events/1/checkout';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETevents--event_id--checkout">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IjJqWTdnUXM0NkZSRlVUZko2VEhtbEE9PSIsInZhbHVlIjoiWEhrTmNMYmlVVkxveUFwdDBxamFpWFZPMmhGdHVaUU1lenRSekI1WnlxMlBvVlhESnNxWlZ0SzQ2azFUVGJVQjVJN2oxMGt6V2JVODRWYWw0VUJoL3JlZ2VLN3dHVUR6UzhPdnJod0dRQndKMHBtRTZqK29JNnZBT2FJVk56NnAiLCJtYWMiOiIzZmIwMzg3NWEwMGYyZGM2NTEzNTMxY2FhNjQwYzcwZGUwMTA5ZWVhMTM0MDBiYzQwMzUyMDQ1Yzk4YmM5NmQ2IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6Ik1zNUtSYXFkUmVtVTl2eDZ2alEzWHc9PSIsInZhbHVlIjoiSEY0MnRLQWdsRDErRkNnemFLVHlFYWdENXNhZDRkWnl5Rk13UWVKN2VrVEtNdXJZVlpZbWlJVUdtc0FGR2NOWTduc2dXdkFiU2NLWU9iTzlHNWlCYzhmdnlXWWlvMTI1ZHhzR3AzTnYwbFgxRDMraFFvVVhTRjZyT3BsSGRCNTAiLCJtYWMiOiI1ZjA1YTJlYzEzZTg1YWU4MjUyMzdjMmZmMDVmZTlhZWY3MjU0OTk0Yzg5MzNjNzMzYTI1ZTNmZjRmYjVmZmIyIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETevents--event_id--checkout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETevents--event_id--checkout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETevents--event_id--checkout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETevents--event_id--checkout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETevents--event_id--checkout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETevents--event_id--checkout" data-method="GET"
      data-path="events/{event_id}/checkout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETevents--event_id--checkout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETevents--event_id--checkout"
                    onclick="tryItOut('GETevents--event_id--checkout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETevents--event_id--checkout"
                    onclick="cancelTryOut('GETevents--event_id--checkout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETevents--event_id--checkout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>events/{event_id}/checkout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETevents--event_id--checkout"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETevents--event_id--checkout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETevents--event_id--checkout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>event_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="event_id"                data-endpoint="GETevents--event_id--checkout"
               value="1"
               data-component="url">
    <br>
<p>The ID of the event. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="checkout-POSTevents--event_id--checkout">POST events/{event_id}/checkout</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTevents--event_id--checkout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/events/1/checkout" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json" \
    --data "{
    \"ticket_category_id\": 17,
    \"quantity\": 10,
    \"holder_name\": \"qeopfuudtdsufvyvddqam\",
    \"email\": \"evert28@example.com\",
    \"payment_provider\": \"airtel\",
    \"phone_number\": \"qcoynlazghdtqtqxbajwb\",
    \"create_account\": true,
    \"password\": \"AKYLk4&gt;SJIrIV#l\",
    \"idempotency_key\": \"ydlsmsjuryvojcybzvrby\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/events/1/checkout"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};

let body = {
    "ticket_category_id": 17,
    "quantity": 10,
    "holder_name": "qeopfuudtdsufvyvddqam",
    "email": "evert28@example.com",
    "payment_provider": "airtel",
    "phone_number": "qcoynlazghdtqtqxbajwb",
    "create_account": true,
    "password": "AKYLk4&gt;SJIrIV#l",
    "idempotency_key": "ydlsmsjuryvojcybzvrby"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/events/1/checkout';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'ticket_category_id' =&gt; 17,
            'quantity' =&gt; 10,
            'holder_name' =&gt; 'qeopfuudtdsufvyvddqam',
            'email' =&gt; 'evert28@example.com',
            'payment_provider' =&gt; 'airtel',
            'phone_number' =&gt; 'qcoynlazghdtqtqxbajwb',
            'create_account' =&gt; true,
            'password' =&gt; 'AKYLk4&gt;SJIrIV#l',
            'idempotency_key' =&gt; 'ydlsmsjuryvojcybzvrby',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTevents--event_id--checkout">
</span>
<span id="execution-results-POSTevents--event_id--checkout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTevents--event_id--checkout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTevents--event_id--checkout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTevents--event_id--checkout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTevents--event_id--checkout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTevents--event_id--checkout" data-method="POST"
      data-path="events/{event_id}/checkout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTevents--event_id--checkout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTevents--event_id--checkout"
                    onclick="tryItOut('POSTevents--event_id--checkout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTevents--event_id--checkout"
                    onclick="cancelTryOut('POSTevents--event_id--checkout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTevents--event_id--checkout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>events/{event_id}/checkout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTevents--event_id--checkout"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTevents--event_id--checkout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTevents--event_id--checkout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>event_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="event_id"                data-endpoint="POSTevents--event_id--checkout"
               value="1"
               data-component="url">
    <br>
<p>The ID of the event. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ticket_category_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="ticket_category_id"                data-endpoint="POSTevents--event_id--checkout"
               value="17"
               data-component="body">
    <br>
<p>Example: <code>17</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>quantity</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="quantity"                data-endpoint="POSTevents--event_id--checkout"
               value="10"
               data-component="body">
    <br>
<p>Must be at least 1. Must not be greater than 20. Example: <code>10</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>holder_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="holder_name"                data-endpoint="POSTevents--event_id--checkout"
               value="qeopfuudtdsufvyvddqam"
               data-component="body">
    <br>
<p>Must not be greater than 120 characters. Example: <code>qeopfuudtdsufvyvddqam</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTevents--event_id--checkout"
               value="evert28@example.com"
               data-component="body">
    <br>
<p>Must be a valid email address. Must not be greater than 255 characters. Example: <code>evert28@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>payment_provider</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="payment_provider"                data-endpoint="POSTevents--event_id--checkout"
               value="airtel"
               data-component="body">
    <br>
<p>Example: <code>airtel</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>mtn</code></li> <li><code>airtel</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone_number"                data-endpoint="POSTevents--event_id--checkout"
               value="qcoynlazghdtqtqxbajwb"
               data-component="body">
    <br>
<p>Must not be greater than 40 characters. Example: <code>qcoynlazghdtqtqxbajwb</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>create_account</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <label data-endpoint="POSTevents--event_id--checkout" style="display: none">
            <input type="radio" name="create_account"
                   value="true"
                   data-endpoint="POSTevents--event_id--checkout"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTevents--event_id--checkout" style="display: none">
            <input type="radio" name="create_account"
                   value="false"
                   data-endpoint="POSTevents--event_id--checkout"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Example: <code>true</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTevents--event_id--checkout"
               value="AKYLk4>SJIrIV#l"
               data-component="body">
    <br>
<p>This field is required when <code>create_account</code> is <code>1</code>. Must be at least 8 characters. Example: <code>AKYLk4&gt;SJIrIV#l</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>idempotency_key</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="idempotency_key"                data-endpoint="POSTevents--event_id--checkout"
               value="ydlsmsjuryvojcybzvrby"
               data-component="body">
    <br>
<p>Must not be greater than 64 characters. Example: <code>ydlsmsjuryvojcybzvrby</code></p>
        </div>
        </form>

                <h1 id="tickets">Tickets</h1>

    <p>View, download, and manage purchased tickets.</p>

                                <h2 id="tickets-GETmy-tickets">GET my-tickets</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETmy-tickets">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/my-tickets" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/my-tickets"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/my-tickets';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETmy-tickets">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6Im40VzZLL1dGZFZ2eXJZVlVJaHQzK3c9PSIsInZhbHVlIjoiRWJTQXF5ZnZJeTA3UmYxNDVQK3RYOHNHd25jbDkwK0RDUlREOWlIU3ZzMjBCbCsyWDBrNEdyb0Q3MmFpUkxLZmFWenJGTTdFUmhIVytBMGc0NGRxVVFycWhnakJNUE54L1U2clhwdjZCTDFKOU1TUCtYaElITlpXbDFMVHlZemMiLCJtYWMiOiI0NjYxNmQzMGU1Y2JmYTVhZTA4YmVkMzNhYTZlYWM0YjQ2MGM2ZjNkYWNjOTVlZjFiNzAxNDIzNjdkN2FhYTkwIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6ImtGWFpacGlmYnpPaDJLTjEvUW9xckE9PSIsInZhbHVlIjoiYXlEOVNtbDBTZ0g3cXJ2ZUhFSm1wS2M0Y3NyMERGaFluVUFCdllKT1pza2NUQklkZ3V5WFVFRTJBMlR4YzZVWmNBVkZ0c1FCR2RvT1g2VjdUR295T1k1QWc1bUpFSUFMNE5HUkoybnBJWGxsTXMwZUhhd1VqaWZmSTZXdnZNdUgiLCJtYWMiOiI5ZTkwNWUzYmQ1MGE4MzdlOTVmODFkODk4OGEzMjAwZDY1MDRkN2JjYjQzY2U4YWNjMjVmM2MwMTU4MjY2NGU0IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETmy-tickets" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETmy-tickets"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETmy-tickets"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETmy-tickets" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETmy-tickets">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETmy-tickets" data-method="GET"
      data-path="my-tickets"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETmy-tickets', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETmy-tickets"
                    onclick="tryItOut('GETmy-tickets');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETmy-tickets"
                    onclick="cancelTryOut('GETmy-tickets');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETmy-tickets"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>my-tickets</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETmy-tickets"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETmy-tickets"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETmy-tickets"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="tickets-GETmy-tickets--id-">GET my-tickets/{id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETmy-tickets--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/my-tickets/9" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/my-tickets/9"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/my-tickets/9';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETmy-tickets--id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IlN3TGlEU1dSWlFld0pxMzZZR1lvNFE9PSIsInZhbHVlIjoiSHl5cmd6QXd3TGswMldGSmw2TitjZ3A3dFhDYjFsRTdGNHkrcUo4aXVBYWNqbks1OEVnVHZCamY4ZURYSEhiRHBwbjc4eUQrZ1JndjVRWnhhWkhvb2JqRTI2WVA2WGRnVVdURVZCdmphRDNJQVJMWWgrbm8zVUd6SDJ5bEhyVzMiLCJtYWMiOiI0MzdlMWYxMzk1OTM5YWYzMDExY2I0Y2JhNzk0NzVhMWFiOTA3OGI2OTQ2NGU1NjZhNjJhY2FhMmMyNjE4NDIwIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6IkNqSlpKL3U3cTh5am1vdVBlaUdkRkE9PSIsInZhbHVlIjoiNkpyTzNBVzl4QkZBUEJOODBvWmJKRG5DemU3dDlMdzllaCtIdWQzWkhVeGVGMmhzMGNzbWhEQkhJYXcrbkFoVllMNVl2ZEdvL2VHRUUwUTUzbXN1NEtOWFU1VmxVYVJ0SXNTdnoyWGxKZktQTzdGYWhCcit2OUxkRHMvN1Q4RkYiLCJtYWMiOiI1NGJlOTJiMmZlMmU2ODFlZWQ3OWJjMzg0M2EzNGJjYmE4OWU4NzQ0MTkyYTM0ZGMwNzhmYzU3MjA0ZmIzNjNjIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETmy-tickets--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETmy-tickets--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETmy-tickets--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETmy-tickets--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETmy-tickets--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETmy-tickets--id-" data-method="GET"
      data-path="my-tickets/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETmy-tickets--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETmy-tickets--id-"
                    onclick="tryItOut('GETmy-tickets--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETmy-tickets--id-"
                    onclick="cancelTryOut('GETmy-tickets--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETmy-tickets--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>my-tickets/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETmy-tickets--id-"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETmy-tickets--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETmy-tickets--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETmy-tickets--id-"
               value="9"
               data-component="url">
    <br>
<p>The ID of the my ticket. Example: <code>9</code></p>
            </div>
                    </form>

                    <h2 id="tickets-GETmy-tickets--ticket_id--download">GET my-tickets/{ticket_id}/download</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETmy-tickets--ticket_id--download">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/my-tickets/9/download" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/my-tickets/9/download"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/my-tickets/9/download';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETmy-tickets--ticket_id--download">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IlVzeVMzT095R2FpQVVuS0NxZUpXZ0E9PSIsInZhbHVlIjoiR1lVc1hyQXQzQTVpV0RxWGNiQURCWUVqdExrNWhUSGlKTnY0N29adndwbFk1NkNRRDFzeUVjdmxQRVVJc3NMMDZnb1VvOTZjdEF3TVpnMmFpRDNabmVKaHVUWWtwc01PUTBxWmxKOFhlcHZJT2xOK3BlNzlzcFRaVUNLRTJINEQiLCJtYWMiOiI1MjdmNzI0MGNlZGIxZGI3YmZlYmI2YzgzMzZkMWM3NTRjYmM2YzkxNzZlYzAxZThjMDgxNDYxNDJlNDIwZjgzIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6IlNXWTh6bVBlcGlpTFZKb09zN0p2R0E9PSIsInZhbHVlIjoiZW1rSXJvTnc2MzRpZjhmaVdyckRic2NpOFl4UWV4MkcrR1Z6azBsd0dEUk9DWGlzM1VXQ3dHeVFTcER4Y0J6cHBDV2U5ZnlXak5ibUExclJCWC9VYXVsOEVDa09ScW1yaGpIR1JrNUNpOFBXUkRKN3QyYjNwTXRsbHppdjEyN2giLCJtYWMiOiIzOGQ5MmUyNjcyMTkyYWI2Y2JjMjcxM2E4MjllNjY1ZmQ0ZmExOWY3YWRlNzQ1NzVjM2Y2YmViOTY2MDU2YjgzIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETmy-tickets--ticket_id--download" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETmy-tickets--ticket_id--download"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETmy-tickets--ticket_id--download"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETmy-tickets--ticket_id--download" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETmy-tickets--ticket_id--download">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETmy-tickets--ticket_id--download" data-method="GET"
      data-path="my-tickets/{ticket_id}/download"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETmy-tickets--ticket_id--download', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETmy-tickets--ticket_id--download"
                    onclick="tryItOut('GETmy-tickets--ticket_id--download');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETmy-tickets--ticket_id--download"
                    onclick="cancelTryOut('GETmy-tickets--ticket_id--download');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETmy-tickets--ticket_id--download"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>my-tickets/{ticket_id}/download</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETmy-tickets--ticket_id--download"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETmy-tickets--ticket_id--download"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETmy-tickets--ticket_id--download"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>ticket_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="ticket_id"                data-endpoint="GETmy-tickets--ticket_id--download"
               value="9"
               data-component="url">
    <br>
<p>The ID of the ticket. Example: <code>9</code></p>
            </div>
                    </form>

                    <h2 id="tickets-GETmy-tickets--ticket_id--pdf">GET my-tickets/{ticket_id}/pdf</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETmy-tickets--ticket_id--pdf">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/my-tickets/9/pdf" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/my-tickets/9/pdf"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/my-tickets/9/pdf';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETmy-tickets--ticket_id--pdf">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IjY0Y2N1QjNkSDZGYy9JV1ZFT1NMSWc9PSIsInZhbHVlIjoiQWE1SDIzeVM1bE80YUxUUnphVUFYdHlNQU41ZGpPTVlqS3JLT3hyS1R0MEdXRTNJTEh0L21aZzJqWEZJaEpvMVBKZkthUUwvS0pYLzY5bkkrQU1xYVpNSXI3bHJYVncwdk5HdUhZZEgzVkxuWWNVN211SXl5bGZDKzRERG13WnUiLCJtYWMiOiIzNGU1NDBjYWE2ZGNmYmZlOGNhNWY2NWFkZjJkMzk5OTU3YjA2ZGE5MjlhMDgwMmM4MzY3ZjM5ZmZiYjNmMGNhIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6IkQxelAvaDFEVmhnOVJQRkwrNnZJdUE9PSIsInZhbHVlIjoiaUNkUEorTjBVaE8zc1FqV3Axek5kTmJYc0JTM3dYQnhzVm9PSi9yazRXUDUyblVHNGhzY1VmZ3FNMlk3N3k4Wm1oT2w4Mm9pcjRxN0NEQVpQemVLUlNKQXZBZnczMU1oVW5nMGdqTjVNWklvL1JsWEZqWW5lbk9VTWNPc1oyYkciLCJtYWMiOiIxOTRhMzQ2YTdiZGQ4YmQ3ZDdjODlhYWY0NDZjNzEyMzk2NjUxN2Y1ZWZkMmU5ZTg1ZGY5OWJiNzA3NDg0ODk3IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETmy-tickets--ticket_id--pdf" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETmy-tickets--ticket_id--pdf"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETmy-tickets--ticket_id--pdf"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETmy-tickets--ticket_id--pdf" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETmy-tickets--ticket_id--pdf">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETmy-tickets--ticket_id--pdf" data-method="GET"
      data-path="my-tickets/{ticket_id}/pdf"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETmy-tickets--ticket_id--pdf', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETmy-tickets--ticket_id--pdf"
                    onclick="tryItOut('GETmy-tickets--ticket_id--pdf');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETmy-tickets--ticket_id--pdf"
                    onclick="cancelTryOut('GETmy-tickets--ticket_id--pdf');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETmy-tickets--ticket_id--pdf"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>my-tickets/{ticket_id}/pdf</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETmy-tickets--ticket_id--pdf"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETmy-tickets--ticket_id--pdf"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETmy-tickets--ticket_id--pdf"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>ticket_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="ticket_id"                data-endpoint="GETmy-tickets--ticket_id--pdf"
               value="9"
               data-component="url">
    <br>
<p>The ID of the ticket. Example: <code>9</code></p>
            </div>
                    </form>

                    <h2 id="tickets-POSTmy-tickets--ticket_id--refund-request">POST my-tickets/{ticket_id}/refund-request</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTmy-tickets--ticket_id--refund-request">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/my-tickets/9/refund-request" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json" \
    --data "{
    \"reason\": \"vmqeopfuudtdsufvyvddq\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/my-tickets/9/refund-request"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};

let body = {
    "reason": "vmqeopfuudtdsufvyvddq"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/my-tickets/9/refund-request';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'reason' =&gt; 'vmqeopfuudtdsufvyvddq',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTmy-tickets--ticket_id--refund-request">
</span>
<span id="execution-results-POSTmy-tickets--ticket_id--refund-request" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTmy-tickets--ticket_id--refund-request"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTmy-tickets--ticket_id--refund-request"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTmy-tickets--ticket_id--refund-request" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTmy-tickets--ticket_id--refund-request">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTmy-tickets--ticket_id--refund-request" data-method="POST"
      data-path="my-tickets/{ticket_id}/refund-request"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTmy-tickets--ticket_id--refund-request', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTmy-tickets--ticket_id--refund-request"
                    onclick="tryItOut('POSTmy-tickets--ticket_id--refund-request');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTmy-tickets--ticket_id--refund-request"
                    onclick="cancelTryOut('POSTmy-tickets--ticket_id--refund-request');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTmy-tickets--ticket_id--refund-request"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>my-tickets/{ticket_id}/refund-request</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTmy-tickets--ticket_id--refund-request"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTmy-tickets--ticket_id--refund-request"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTmy-tickets--ticket_id--refund-request"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>ticket_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="ticket_id"                data-endpoint="POSTmy-tickets--ticket_id--refund-request"
               value="9"
               data-component="url">
    <br>
<p>The ID of the ticket. Example: <code>9</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>reason</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="reason"                data-endpoint="POSTmy-tickets--ticket_id--refund-request"
               value="vmqeopfuudtdsufvyvddq"
               data-component="body">
    <br>
<p>Must not be greater than 500 characters. Example: <code>vmqeopfuudtdsufvyvddq</code></p>
        </div>
        </form>

                <h1 id="gate-portal">Gate Portal</h1>

    <p>Gate agent dashboard and walk-in ticket sales.</p>

                                <h2 id="gate-portal-GETgate-portal">GET gate-portal</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETgate-portal">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/gate-portal" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/gate-portal"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/gate-portal';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETgate-portal">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6Iloxa0xZS09JTEUwTmdocjRFM2lLN0E9PSIsInZhbHVlIjoiQ2ZHYWRmWmc0U2F2R3d3MW84dEpHZ0lsZXlBZnBtUGhuZ3czY3M3RmtlaUFzM0R1SkNxWmdBMXNVYkJNa2pLcmVMaGdxUGlER2pnY2FLRWZ3Z1QydnJycHpWcUpRS3JxNDB5VlVnZ3J1cXVWZ3pYejJUZ2RiSmRZVzJSeDViVGoiLCJtYWMiOiJlNTJhOGY3Nzk2YTM0MDg3ODJlODBlOGUyOWIzZDgzNGQ2ODA0NzdmMjU4YjBlYjc0OTQ3ZWM0NTBmZjA1ZTI2IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6IndQTGhIalBneUx0QkUrUU1YZXJGb2c9PSIsInZhbHVlIjoiRGpXNUxlY0JnVjgvazlRSUI4SkZva1hUK2Q3cnZPN0VHUkdGaEltdW54T1dXRHRGcWNaemtJZ1g5dVZWNmc2WFdwNEZocEJ5dDkxSGxUeSs3OTdHSzhLNGE1d1pEN0NVQURQaWRkNllhRFg0cmlsblRDWjM5aVVxaWliZ0VUamkiLCJtYWMiOiJkMzRlODZjYmRkNTljYzk2ZTRkZDU4ZjZiNjdlMDE0NGY4MGUyMjg4MzgxZGNiYTcwMjFjNGI4YjlmOTY0MTg0IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETgate-portal" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETgate-portal"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETgate-portal"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETgate-portal" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETgate-portal">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETgate-portal" data-method="GET"
      data-path="gate-portal"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETgate-portal', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETgate-portal"
                    onclick="tryItOut('GETgate-portal');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETgate-portal"
                    onclick="cancelTryOut('GETgate-portal');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETgate-portal"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>gate-portal</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETgate-portal"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETgate-portal"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETgate-portal"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="gate-portal-POSTgate-portal-walk-in-sale">POST gate-portal/walk-in-sale</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTgate-portal-walk-in-sale">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/gate-portal/walk-in-sale" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json" \
    --data "{
    \"event_id\": 17,
    \"ticket_category_id\": 17,
    \"holder_name\": \"mqeopfuudtdsufvyvddqa\",
    \"email\": \"eloisa.harber@example.com\",
    \"phone_number\": \"fqcoynlazghdtqtqxbajw\",
    \"quantity\": 2,
    \"payment_channel\": \"airtel\",
    \"collector_reference\": \"pilpmufinllwloauydlsm\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/gate-portal/walk-in-sale"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};

let body = {
    "event_id": 17,
    "ticket_category_id": 17,
    "holder_name": "mqeopfuudtdsufvyvddqa",
    "email": "eloisa.harber@example.com",
    "phone_number": "fqcoynlazghdtqtqxbajw",
    "quantity": 2,
    "payment_channel": "airtel",
    "collector_reference": "pilpmufinllwloauydlsm"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/gate-portal/walk-in-sale';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'event_id' =&gt; 17,
            'ticket_category_id' =&gt; 17,
            'holder_name' =&gt; 'mqeopfuudtdsufvyvddqa',
            'email' =&gt; 'eloisa.harber@example.com',
            'phone_number' =&gt; 'fqcoynlazghdtqtqxbajw',
            'quantity' =&gt; 2,
            'payment_channel' =&gt; 'airtel',
            'collector_reference' =&gt; 'pilpmufinllwloauydlsm',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTgate-portal-walk-in-sale">
</span>
<span id="execution-results-POSTgate-portal-walk-in-sale" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTgate-portal-walk-in-sale"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTgate-portal-walk-in-sale"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTgate-portal-walk-in-sale" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTgate-portal-walk-in-sale">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTgate-portal-walk-in-sale" data-method="POST"
      data-path="gate-portal/walk-in-sale"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTgate-portal-walk-in-sale', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTgate-portal-walk-in-sale"
                    onclick="tryItOut('POSTgate-portal-walk-in-sale');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTgate-portal-walk-in-sale"
                    onclick="cancelTryOut('POSTgate-portal-walk-in-sale');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTgate-portal-walk-in-sale"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>gate-portal/walk-in-sale</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTgate-portal-walk-in-sale"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTgate-portal-walk-in-sale"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTgate-portal-walk-in-sale"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>event_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="event_id"                data-endpoint="POSTgate-portal-walk-in-sale"
               value="17"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the events table. Example: <code>17</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ticket_category_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="ticket_category_id"                data-endpoint="POSTgate-portal-walk-in-sale"
               value="17"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the ticket_categories table. Example: <code>17</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>holder_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="holder_name"                data-endpoint="POSTgate-portal-walk-in-sale"
               value="mqeopfuudtdsufvyvddqa"
               data-component="body">
    <br>
<p>Must not be greater than 120 characters. Example: <code>mqeopfuudtdsufvyvddqa</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTgate-portal-walk-in-sale"
               value="eloisa.harber@example.com"
               data-component="body">
    <br>
<p>Must be a valid email address. Must not be greater than 255 characters. Example: <code>eloisa.harber@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone_number"                data-endpoint="POSTgate-portal-walk-in-sale"
               value="fqcoynlazghdtqtqxbajw"
               data-component="body">
    <br>
<p>Must not be greater than 40 characters. Example: <code>fqcoynlazghdtqtqxbajw</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>quantity</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="quantity"                data-endpoint="POSTgate-portal-walk-in-sale"
               value="2"
               data-component="body">
    <br>
<p>Must be at least 1. Must not be greater than 20. Example: <code>2</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>payment_channel</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="payment_channel"                data-endpoint="POSTgate-portal-walk-in-sale"
               value="airtel"
               data-component="body">
    <br>
<p>Example: <code>airtel</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>mtn</code></li> <li><code>airtel</code></li> <li><code>cash</code></li> <li><code>pos</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>collector_reference</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="collector_reference"                data-endpoint="POSTgate-portal-walk-in-sale"
               value="pilpmufinllwloauydlsm"
               data-component="body">
    <br>
<p>Must not be greater than 120 characters. Example: <code>pilpmufinllwloauydlsm</code></p>
        </div>
        </form>

                <h1 id="ticket-verification">Ticket Verification</h1>

    <p>Scan and verify tickets at the event entrance.</p>

                                <h2 id="ticket-verification-GETticket-verification">GET ticket-verification</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETticket-verification">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/ticket-verification" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/ticket-verification"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/ticket-verification';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETticket-verification">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IkdqeGdTc3dXRmNFQmlqSHRJVW1XbHc9PSIsInZhbHVlIjoiNExMVkhZZHhGTUtPSDJBSTlkT1JzR0FCeHR1MWZQK05KbEpjcFBoQUswOU5sZ0didFUvNGVUVVU0Snk5Zk5DUXJnUHVHWi9sUVNaSG03WE0rTlo5R3NkbTVHNUppR1MxRXlSVnR5YUp4RWhLczhwZHppNjVQNDgvd0Rhd29XOEciLCJtYWMiOiI5ODM1ZTk4ZjY5N2ExMTlkZjQ5MDVjOWNlNDg4M2EzZGEwODUwODc3M2ZhMTE2MzNiZjM0MGRmNTdmOGUwNGVlIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6ImUzWlg1YmhrUk9DNWZLMDk4L0Z2d3c9PSIsInZhbHVlIjoiSGFXU3lJVUFUZVNNeTJ5ZS94SThmVVN6WnE1K0R2TGlUTE5JbU5GVWxyQ0lrTGtUYUtvWEg1b2J3NWt3VCtGL0lFcWo0TEVFZTg2dENGNlVqM2hsN0RzbG9TblczcFFTR2phdHhCSzdRYWdvaXJFcTNTbm9rTVlGa29SMzZYUkMiLCJtYWMiOiJjZmEyMmM4MjQyNzAzM2NkMGZiZWJlNTUwNDI2ODAwOGI3YjAzMGM4ZGMxNjk2ODlkOTk0MDI5MDY5ZjFjZGQ2IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETticket-verification" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETticket-verification"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETticket-verification"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETticket-verification" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETticket-verification">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETticket-verification" data-method="GET"
      data-path="ticket-verification"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETticket-verification', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETticket-verification"
                    onclick="tryItOut('GETticket-verification');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETticket-verification"
                    onclick="cancelTryOut('GETticket-verification');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETticket-verification"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>ticket-verification</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETticket-verification"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETticket-verification"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETticket-verification"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="ticket-verification-POSTticket-verification">POST ticket-verification</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTticket-verification">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/ticket-verification" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json" \
    --data "{
    \"selected_event_id\": 17,
    \"ticket_code\": \"consequatur\",
    \"scanned_payload\": \"consequatur\",
    \"lookup\": \"mqeopfuudtdsufvyvddqa\",
    \"device_id\": \"mniihfqcoynlazghdtqtq\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/ticket-verification"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};

let body = {
    "selected_event_id": 17,
    "ticket_code": "consequatur",
    "scanned_payload": "consequatur",
    "lookup": "mqeopfuudtdsufvyvddqa",
    "device_id": "mniihfqcoynlazghdtqtq"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/ticket-verification';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'selected_event_id' =&gt; 17,
            'ticket_code' =&gt; 'consequatur',
            'scanned_payload' =&gt; 'consequatur',
            'lookup' =&gt; 'mqeopfuudtdsufvyvddqa',
            'device_id' =&gt; 'mniihfqcoynlazghdtqtq',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTticket-verification">
</span>
<span id="execution-results-POSTticket-verification" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTticket-verification"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTticket-verification"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTticket-verification" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTticket-verification">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTticket-verification" data-method="POST"
      data-path="ticket-verification"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTticket-verification', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTticket-verification"
                    onclick="tryItOut('POSTticket-verification');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTticket-verification"
                    onclick="cancelTryOut('POSTticket-verification');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTticket-verification"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>ticket-verification</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTticket-verification"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTticket-verification"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTticket-verification"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>selected_event_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="selected_event_id"                data-endpoint="POSTticket-verification"
               value="17"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the events table. Example: <code>17</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ticket_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="ticket_code"                data-endpoint="POSTticket-verification"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>scanned_payload</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="scanned_payload"                data-endpoint="POSTticket-verification"
               value="consequatur"
               data-component="body">
    <br>
<p>Example: <code>consequatur</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>lookup</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="lookup"                data-endpoint="POSTticket-verification"
               value="mqeopfuudtdsufvyvddqa"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>mqeopfuudtdsufvyvddqa</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_id"                data-endpoint="POSTticket-verification"
               value="mniihfqcoynlazghdtqtq"
               data-component="body">
    <br>
<p>Must not be greater than 120 characters. Example: <code>mniihfqcoynlazghdtqtq</code></p>
        </div>
        </form>

                    <h2 id="ticket-verification-POSTticket-verification-scan">POST ticket-verification/scan</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTticket-verification-scan">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/ticket-verification/scan" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json" \
    --data "{
    \"selected_event_id\": 17,
    \"ticket_code\": \"mqeopfuudtdsufvyvddqa\",
    \"scanned_payload\": \"mniihfqcoynlazghdtqtq\",
    \"device_id\": \"xbajwbpilpmufinllwloa\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/ticket-verification/scan"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};

let body = {
    "selected_event_id": 17,
    "ticket_code": "mqeopfuudtdsufvyvddqa",
    "scanned_payload": "mniihfqcoynlazghdtqtq",
    "device_id": "xbajwbpilpmufinllwloa"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/ticket-verification/scan';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'selected_event_id' =&gt; 17,
            'ticket_code' =&gt; 'mqeopfuudtdsufvyvddqa',
            'scanned_payload' =&gt; 'mniihfqcoynlazghdtqtq',
            'device_id' =&gt; 'xbajwbpilpmufinllwloa',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTticket-verification-scan">
</span>
<span id="execution-results-POSTticket-verification-scan" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTticket-verification-scan"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTticket-verification-scan"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTticket-verification-scan" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTticket-verification-scan">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTticket-verification-scan" data-method="POST"
      data-path="ticket-verification/scan"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTticket-verification-scan', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTticket-verification-scan"
                    onclick="tryItOut('POSTticket-verification-scan');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTticket-verification-scan"
                    onclick="cancelTryOut('POSTticket-verification-scan');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTticket-verification-scan"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>ticket-verification/scan</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTticket-verification-scan"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTticket-verification-scan"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTticket-verification-scan"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>selected_event_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="selected_event_id"                data-endpoint="POSTticket-verification-scan"
               value="17"
               data-component="body">
    <br>
<p>Example: <code>17</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>ticket_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="ticket_code"                data-endpoint="POSTticket-verification-scan"
               value="mqeopfuudtdsufvyvddqa"
               data-component="body">
    <br>
<p>Must not be greater than 120 characters. Example: <code>mqeopfuudtdsufvyvddqa</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>scanned_payload</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="scanned_payload"                data-endpoint="POSTticket-verification-scan"
               value="mniihfqcoynlazghdtqtq"
               data-component="body">
    <br>
<p>Must not be greater than 2000 characters. Example: <code>mniihfqcoynlazghdtqtq</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_id"                data-endpoint="POSTticket-verification-scan"
               value="xbajwbpilpmufinllwloa"
               data-component="body">
    <br>
<p>Must not be greater than 120 characters. Example: <code>xbajwbpilpmufinllwloa</code></p>
        </div>
        </form>

                    <h2 id="ticket-verification-GETticket-verification-export">GET ticket-verification/export</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETticket-verification-export">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/ticket-verification/export" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json" \
    --data "{
    \"event_id\": 17
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/ticket-verification/export"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};

let body = {
    "event_id": 17
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/ticket-verification/export';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'event_id' =&gt; 17,
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETticket-verification-export">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6Im52VVJQRmZYZUJEYXhLRGFhRUwyWmc9PSIsInZhbHVlIjoiT3k2NGhYdUh6S25YQU5jcEpsemRaUVBxeVd4RWVlRnR0QW1seW5mREY1T20zbElWY2xCWllidmRMb3dXaUdHT3crYUN1Z0JJS3lCMTZhbWdGbXZ4bmNUaVhDWWlvREQxU1F4SWFrWGRJOWcwTGc4VjQyMUFUK1ZoekJGVTd4dUwiLCJtYWMiOiJlNDBiZDlhOTIwODBkOGE2YTdkMzI2MWZhNTMwNjBkZmYyNzk1ZmVmOGNjNjc4NDMyNWMxOTUyMDExNTkwNThmIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6IklwdDR1L1JyNkpxWm5iVjR1aDhrRFE9PSIsInZhbHVlIjoiQWlsY0plYjl4elJxaHhRU0xFbnNhYkowcm42WnMwdmRmY3ppSzdJK0VjeWFlMmtoMUJIb3lHM2FHVDVNNk9jcUMzZmthUkUrQ2xWc2p6NW01dXVlTWtKWFRXQjFEcEZkc0Rtcy9WK1NTUDl6Y2xSeEFQTmxQQ3VIZ05KQjVBdG4iLCJtYWMiOiI5ZDllOWYzZThkODAyZjZmNWQ3OWY5ODQ0OTQyNDA4Mzg3ZGY1NDcwNzFlZWMwYTljM2MzYmJkYjc1NDlhNWQ3IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETticket-verification-export" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETticket-verification-export"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETticket-verification-export"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETticket-verification-export" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETticket-verification-export">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETticket-verification-export" data-method="GET"
      data-path="ticket-verification/export"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETticket-verification-export', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETticket-verification-export"
                    onclick="tryItOut('GETticket-verification-export');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETticket-verification-export"
                    onclick="cancelTryOut('GETticket-verification-export');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETticket-verification-export"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>ticket-verification/export</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETticket-verification-export"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETticket-verification-export"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETticket-verification-export"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>event_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="event_id"                data-endpoint="GETticket-verification-export"
               value="17"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the events table. Example: <code>17</code></p>
        </div>
        </form>

                <h1 id="admin-payments">Admin — Payments</h1>

    <p>Admin operations: confirm, refund, and resend payment notifications.</p>

                                <h2 id="admin-payments-GETadmin-payments">GET admin/payments</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETadmin-payments">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/admin/payments" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/admin/payments"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/admin/payments';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETadmin-payments">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IkppYklpOXdyUmV6NDhoUlMwUVdiOUE9PSIsInZhbHVlIjoiSDZ2QVhJWmtnNWxiQTlxNEFqVlVOL2d5L3dBTVpBQ2FPK25oZzBvRnVibkpPMUdhOFNPTzBXNFU0ZkJpN0VqbFdXMnFHSHlnMFVjdys0K3NCRC9wdjk5Q0JjeUUzcWM1OEY5SW9iUWdpSGZHRHRTb3k3NU9QV3h1QVNhcVZlQTMiLCJtYWMiOiJlMDFjMTM3NTU4YjA4ODNiMzcyNTU2Mzg2MzNlZDNjNDMwYTg5ZWI4MWJmNGY2ZDVlYTdlMTE1NDQ3YjMzODk0IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6ImVpZGN2QVd0aUxUMzI0RHEvYWFaWVE9PSIsInZhbHVlIjoiYm9jQ0dveWlxcDdKNUVFZnZhSlJEbzZYR0hZRWxORklFSkhvaTFrQlowd2NBbXI2dkhFOUZ4UnNFNFBsa1ZVQStQQ0d6S2JDQmh2Tk9XOXZ3eEVqQ0pGNEhzbUZSZ3B5ck52czdtck9GRmVyRjcwaG95M1g4Q2labFV2Rno5OHoiLCJtYWMiOiJiNzUyYWJlMzYyYTM0MzBiMzIwOTg0YTE3N2I3Y2IwNWQ3ZTQ4MWM0YmM5ZThhZjhhZTc5ZGQyMzAwNzIyZjQ2IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETadmin-payments" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETadmin-payments"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETadmin-payments"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETadmin-payments" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETadmin-payments">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETadmin-payments" data-method="GET"
      data-path="admin/payments"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETadmin-payments', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETadmin-payments"
                    onclick="tryItOut('GETadmin-payments');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETadmin-payments"
                    onclick="cancelTryOut('GETadmin-payments');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETadmin-payments"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>admin/payments</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETadmin-payments"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETadmin-payments"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETadmin-payments"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="admin-payments-POSTadmin-payments--paymentTransaction_id--resend">POST admin/payments/{paymentTransaction_id}/resend</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTadmin-payments--paymentTransaction_id--resend">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/admin/payments/15/resend" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/admin/payments/15/resend"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/admin/payments/15/resend';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTadmin-payments--paymentTransaction_id--resend">
</span>
<span id="execution-results-POSTadmin-payments--paymentTransaction_id--resend" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTadmin-payments--paymentTransaction_id--resend"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTadmin-payments--paymentTransaction_id--resend"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTadmin-payments--paymentTransaction_id--resend" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTadmin-payments--paymentTransaction_id--resend">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTadmin-payments--paymentTransaction_id--resend" data-method="POST"
      data-path="admin/payments/{paymentTransaction_id}/resend"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTadmin-payments--paymentTransaction_id--resend', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTadmin-payments--paymentTransaction_id--resend"
                    onclick="tryItOut('POSTadmin-payments--paymentTransaction_id--resend');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTadmin-payments--paymentTransaction_id--resend"
                    onclick="cancelTryOut('POSTadmin-payments--paymentTransaction_id--resend');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTadmin-payments--paymentTransaction_id--resend"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>admin/payments/{paymentTransaction_id}/resend</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTadmin-payments--paymentTransaction_id--resend"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTadmin-payments--paymentTransaction_id--resend"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTadmin-payments--paymentTransaction_id--resend"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>paymentTransaction_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="paymentTransaction_id"                data-endpoint="POSTadmin-payments--paymentTransaction_id--resend"
               value="15"
               data-component="url">
    <br>
<p>The ID of the paymentTransaction. Example: <code>15</code></p>
            </div>
                    </form>

                    <h2 id="admin-payments-POSTadmin-payments--paymentTransaction_id--confirm">POST admin/payments/{paymentTransaction_id}/confirm</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTadmin-payments--paymentTransaction_id--confirm">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/admin/payments/15/confirm" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/admin/payments/15/confirm"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/admin/payments/15/confirm';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTadmin-payments--paymentTransaction_id--confirm">
</span>
<span id="execution-results-POSTadmin-payments--paymentTransaction_id--confirm" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTadmin-payments--paymentTransaction_id--confirm"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTadmin-payments--paymentTransaction_id--confirm"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTadmin-payments--paymentTransaction_id--confirm" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTadmin-payments--paymentTransaction_id--confirm">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTadmin-payments--paymentTransaction_id--confirm" data-method="POST"
      data-path="admin/payments/{paymentTransaction_id}/confirm"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTadmin-payments--paymentTransaction_id--confirm', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTadmin-payments--paymentTransaction_id--confirm"
                    onclick="tryItOut('POSTadmin-payments--paymentTransaction_id--confirm');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTadmin-payments--paymentTransaction_id--confirm"
                    onclick="cancelTryOut('POSTadmin-payments--paymentTransaction_id--confirm');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTadmin-payments--paymentTransaction_id--confirm"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>admin/payments/{paymentTransaction_id}/confirm</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTadmin-payments--paymentTransaction_id--confirm"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTadmin-payments--paymentTransaction_id--confirm"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTadmin-payments--paymentTransaction_id--confirm"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>paymentTransaction_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="paymentTransaction_id"                data-endpoint="POSTadmin-payments--paymentTransaction_id--confirm"
               value="15"
               data-component="url">
    <br>
<p>The ID of the paymentTransaction. Example: <code>15</code></p>
            </div>
                    </form>

                    <h2 id="admin-payments-POSTadmin-payments--paymentTransaction_id--refund">POST admin/payments/{paymentTransaction_id}/refund</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTadmin-payments--paymentTransaction_id--refund">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/admin/payments/15/refund" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json" \
    --data "{
    \"reason\": \"vmqeopfuudtdsufvyvddq\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/admin/payments/15/refund"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};

let body = {
    "reason": "vmqeopfuudtdsufvyvddq"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/admin/payments/15/refund';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
        'json' =&gt; [
            'reason' =&gt; 'vmqeopfuudtdsufvyvddq',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTadmin-payments--paymentTransaction_id--refund">
</span>
<span id="execution-results-POSTadmin-payments--paymentTransaction_id--refund" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTadmin-payments--paymentTransaction_id--refund"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTadmin-payments--paymentTransaction_id--refund"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTadmin-payments--paymentTransaction_id--refund" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTadmin-payments--paymentTransaction_id--refund">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTadmin-payments--paymentTransaction_id--refund" data-method="POST"
      data-path="admin/payments/{paymentTransaction_id}/refund"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTadmin-payments--paymentTransaction_id--refund', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTadmin-payments--paymentTransaction_id--refund"
                    onclick="tryItOut('POSTadmin-payments--paymentTransaction_id--refund');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTadmin-payments--paymentTransaction_id--refund"
                    onclick="cancelTryOut('POSTadmin-payments--paymentTransaction_id--refund');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTadmin-payments--paymentTransaction_id--refund"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>admin/payments/{paymentTransaction_id}/refund</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTadmin-payments--paymentTransaction_id--refund"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTadmin-payments--paymentTransaction_id--refund"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTadmin-payments--paymentTransaction_id--refund"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>paymentTransaction_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="paymentTransaction_id"                data-endpoint="POSTadmin-payments--paymentTransaction_id--refund"
               value="15"
               data-component="url">
    <br>
<p>The ID of the paymentTransaction. Example: <code>15</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>reason</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="reason"                data-endpoint="POSTadmin-payments--paymentTransaction_id--refund"
               value="vmqeopfuudtdsufvyvddq"
               data-component="body">
    <br>
<p>Must not be greater than 500 characters. Example: <code>vmqeopfuudtdsufvyvddq</code></p>
        </div>
        </form>

                <h1 id="webhooks">Webhooks</h1>

    <p>Incoming callbacks from external payment providers. Called by MarzPay, not by the browser.</p>

                                <h2 id="webhooks-POSTpayments-marzepay-webhook">POST payments/marzepay/webhook</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTpayments-marzepay-webhook">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/payments/marzepay/webhook" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/payments/marzepay/webhook"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/payments/marzepay/webhook';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTpayments-marzepay-webhook">
</span>
<span id="execution-results-POSTpayments-marzepay-webhook" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTpayments-marzepay-webhook"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTpayments-marzepay-webhook"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTpayments-marzepay-webhook" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTpayments-marzepay-webhook">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTpayments-marzepay-webhook" data-method="POST"
      data-path="payments/marzepay/webhook"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTpayments-marzepay-webhook', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTpayments-marzepay-webhook"
                    onclick="tryItOut('POSTpayments-marzepay-webhook');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTpayments-marzepay-webhook"
                    onclick="cancelTryOut('POSTpayments-marzepay-webhook');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTpayments-marzepay-webhook"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>payments/marzepay/webhook</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTpayments-marzepay-webhook"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTpayments-marzepay-webhook"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTpayments-marzepay-webhook"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="general">General</h1>

    

                                <h2 id="general-GETfilament-exports--export_id--download">GET filament/exports/{export_id}/download</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETfilament-exports--export_id--download">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/filament/exports/17/download" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/filament/exports/17/download"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/filament/exports/17/download';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETfilament-exports--export_id--download">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IjBBeWRLNStDUmxTRlBNZVc0bTZxMVE9PSIsInZhbHVlIjoiL1ZvU3YrVHpNMGxqS3phZWlyUlludVowUmk5ZnlDT1NhdjFUdGpscVcrREFxUW1HWS9FLzhqdjBrUzlqSlZTZU1jL0VvaldVSTA1eU4yVnRDZGJhOTlnUzhyUEdaWmNaakZwQVR3cnZ1UTZqK1FJNGJjOTNWWjZvdEtZVTBDVFgiLCJtYWMiOiI0OGY2MThhY2Q5YjI3MjZlNGM1ZTcyM2I3Njc3MTFlYzZhYWIwNjBjYjYzZjc1MzlhMzc4OGFiMzU0OWMxNmI2IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:54 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6IkhneXJjUnZaWFJhWmZxb1ArMVl6SUE9PSIsInZhbHVlIjoiTS9rQ1pvRGRXY2JCNTJ4cEZDS2VubUFtQmV6Q3JXbVhGb1hsUUNEMVRNYXJrZ0d1c1pXN0hHMld5MDdLTDUxaVRmZitFMFBqb0NEcmZTbmZxVTZOWFNFVEsvcVZNS3R5S05acHlnZmNaRHMzZHR5aCsxSXhDMzV3QUZXUC9vMlUiLCJtYWMiOiIxMzAyYTExM2ZjZjczMmFkYjhjZjMzNmIxYjY2MmJmNmZlYTIzMzBjNmZlYzYzNjFjZTg5YWUxNzNlMmE4ZTE0IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:54 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETfilament-exports--export_id--download" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETfilament-exports--export_id--download"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETfilament-exports--export_id--download"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETfilament-exports--export_id--download" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETfilament-exports--export_id--download">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETfilament-exports--export_id--download" data-method="GET"
      data-path="filament/exports/{export_id}/download"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETfilament-exports--export_id--download', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETfilament-exports--export_id--download"
                    onclick="tryItOut('GETfilament-exports--export_id--download');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETfilament-exports--export_id--download"
                    onclick="cancelTryOut('GETfilament-exports--export_id--download');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETfilament-exports--export_id--download"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>filament/exports/{export_id}/download</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETfilament-exports--export_id--download"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETfilament-exports--export_id--download"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETfilament-exports--export_id--download"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>export_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="export_id"                data-endpoint="GETfilament-exports--export_id--download"
               value="17"
               data-component="url">
    <br>
<p>The ID of the export. Example: <code>17</code></p>
            </div>
                    </form>

                    <h2 id="general-GETfilament-imports--import_id--failed-rows-download">GET filament/imports/{import_id}/failed-rows/download</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETfilament-imports--import_id--failed-rows-download">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/filament/imports/17/failed-rows/download" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/filament/imports/17/failed-rows/download"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/filament/imports/17/failed-rows/download';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETfilament-imports--import_id--failed-rows-download">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IjBDNWhWRVBXaHJtbUozQXhJRjNjSHc9PSIsInZhbHVlIjoiTXZuNTVWWFR3T2RRVXN2dE4zTWRqRG14eFdTSjNqbHNMUHJ4UUxOVXJZR0t3dHJBeUVuK2RwWmprYkdCQ21kQUpSQU9zaCsyWGZkSjlLb2FnWHpNK0pPQTVqRTduOFJpNFR5QUF2b25IQWZjS0I4emk3a3FsZHFXQTZISlRqTlgiLCJtYWMiOiIyODVmMGM5ZmIzY2ZmNGY3ZGQ2M2I0YmQwYTBiMWM2NDNkMTdlY2RkMGI1YWNiMDhjM2IxZDUxNjY2NTkzMDk1IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:54 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6IkpHQVRNemRDdnRMOG40d0l0YVhiWEE9PSIsInZhbHVlIjoiV3h2M3Jpa3lZL2VWZ2VBVjNHSXVvVzZLdXlHNkNjV1loNGJxR0dYYWo1VkpjazVnN0oyRXhhSzNsam5teGlGU1dwWTlib29UdDkrQ2ZaaXNScGg4K280VU03SjRYSUUwb0JSN1loT3NacTB4RGY3VkNlRDlyd1dCbHVkbk9BZWciLCJtYWMiOiJhNTc4YjU0ZDQ3NDQ1MzNlOWM0ZTkyZWQ1YWRjYmQyZjI2MWI5ZDZiMzg2MDA1MmI5NTU1YTM4ODE3ZGJkNjc2IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:54 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETfilament-imports--import_id--failed-rows-download" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETfilament-imports--import_id--failed-rows-download"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETfilament-imports--import_id--failed-rows-download"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETfilament-imports--import_id--failed-rows-download" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETfilament-imports--import_id--failed-rows-download">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETfilament-imports--import_id--failed-rows-download" data-method="GET"
      data-path="filament/imports/{import_id}/failed-rows/download"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETfilament-imports--import_id--failed-rows-download', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETfilament-imports--import_id--failed-rows-download"
                    onclick="tryItOut('GETfilament-imports--import_id--failed-rows-download');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETfilament-imports--import_id--failed-rows-download"
                    onclick="cancelTryOut('GETfilament-imports--import_id--failed-rows-download');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETfilament-imports--import_id--failed-rows-download"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>filament/imports/{import_id}/failed-rows/download</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETfilament-imports--import_id--failed-rows-download"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETfilament-imports--import_id--failed-rows-download"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETfilament-imports--import_id--failed-rows-download"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>import_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="import_id"                data-endpoint="GETfilament-imports--import_id--failed-rows-download"
               value="17"
               data-component="url">
    <br>
<p>The ID of the import. Example: <code>17</code></p>
            </div>
                    </form>

                    <h2 id="general-GETdashboard">GET dashboard</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETdashboard">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/dashboard" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/dashboard"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/dashboard';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETdashboard">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: laravel-session=eyJpdiI6Ik5OMkJCZ1NBVmVtdWdJenBBTU1VdXc9PSIsInZhbHVlIjoiSWZ4bnRVQU1lTlIxdTlzMEpReEErcW5nSlR2REwybTcyUU1GenZabkNNWVFLbHpRZzdlQmhFamlaVmsxM1JjV2xKa3U5aW5UbllDdVdob2huMjdWcS9mSlBCdnpxMFVQc3puMjMxUll5Qk14ZlVEK3VGWmlLdCs2SFBWaGpkbE0iLCJtYWMiOiJlNjkwZWI0ZGE0OWU2NjNlYzdhMDc4N2Y1NTE4NDA5NmM4NjA0M2YzZjQwZTk0OTAwYzg2ZGEyM2ZmNTQ1NmM3IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:54 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETdashboard" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETdashboard"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETdashboard"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETdashboard" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETdashboard">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETdashboard" data-method="GET"
      data-path="dashboard"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETdashboard', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETdashboard"
                    onclick="tryItOut('GETdashboard');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETdashboard"
                    onclick="cancelTryOut('GETdashboard');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETdashboard"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>dashboard</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETdashboard"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETdashboard"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETdashboard"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="general-POSTlivewire-dbf87c49-update">POST livewire-dbf87c49/update</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTlivewire-dbf87c49-update">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/livewire-dbf87c49/update" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/livewire-dbf87c49/update"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/livewire-dbf87c49/update';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTlivewire-dbf87c49-update">
</span>
<span id="execution-results-POSTlivewire-dbf87c49-update" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTlivewire-dbf87c49-update"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTlivewire-dbf87c49-update"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTlivewire-dbf87c49-update" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTlivewire-dbf87c49-update">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTlivewire-dbf87c49-update" data-method="POST"
      data-path="livewire-dbf87c49/update"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTlivewire-dbf87c49-update', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTlivewire-dbf87c49-update"
                    onclick="tryItOut('POSTlivewire-dbf87c49-update');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTlivewire-dbf87c49-update"
                    onclick="cancelTryOut('POSTlivewire-dbf87c49-update');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTlivewire-dbf87c49-update"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>livewire-dbf87c49/update</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTlivewire-dbf87c49-update"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTlivewire-dbf87c49-update"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTlivewire-dbf87c49-update"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="general-GETlivewire-dbf87c49-livewire-js">GET livewire-dbf87c49/livewire.js</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETlivewire-dbf87c49-livewire-js">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/livewire-dbf87c49/livewire.js" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/livewire-dbf87c49/livewire.js"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/livewire-dbf87c49/livewire.js';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETlivewire-dbf87c49-livewire-js">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">content-type: application/javascript; charset=utf-8
expires: Fri, 23 Apr 2027 07:30:54 GMT
cache-control: max-age=31536000, public
accept-ranges: bytes
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;"></code>
 </pre>
    </span>
<span id="execution-results-GETlivewire-dbf87c49-livewire-js" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETlivewire-dbf87c49-livewire-js"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETlivewire-dbf87c49-livewire-js"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETlivewire-dbf87c49-livewire-js" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETlivewire-dbf87c49-livewire-js">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETlivewire-dbf87c49-livewire-js" data-method="GET"
      data-path="livewire-dbf87c49/livewire.js"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETlivewire-dbf87c49-livewire-js', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETlivewire-dbf87c49-livewire-js"
                    onclick="tryItOut('GETlivewire-dbf87c49-livewire-js');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETlivewire-dbf87c49-livewire-js"
                    onclick="cancelTryOut('GETlivewire-dbf87c49-livewire-js');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETlivewire-dbf87c49-livewire-js"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>livewire-dbf87c49/livewire.js</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETlivewire-dbf87c49-livewire-js"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETlivewire-dbf87c49-livewire-js"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETlivewire-dbf87c49-livewire-js"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="general-GETlivewire-dbf87c49-livewire-min-js-map">GET livewire-dbf87c49/livewire.min.js.map</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETlivewire-dbf87c49-livewire-min-js-map">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/livewire-dbf87c49/livewire.min.js.map" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/livewire-dbf87c49/livewire.min.js.map"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/livewire-dbf87c49/livewire.min.js.map';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETlivewire-dbf87c49-livewire-min-js-map">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">content-type: application/javascript; charset=utf-8
expires: Fri, 23 Apr 2027 07:30:54 GMT
cache-control: max-age=31536000, public
accept-ranges: bytes
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;"></code>
 </pre>
    </span>
<span id="execution-results-GETlivewire-dbf87c49-livewire-min-js-map" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETlivewire-dbf87c49-livewire-min-js-map"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETlivewire-dbf87c49-livewire-min-js-map"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETlivewire-dbf87c49-livewire-min-js-map" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETlivewire-dbf87c49-livewire-min-js-map">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETlivewire-dbf87c49-livewire-min-js-map" data-method="GET"
      data-path="livewire-dbf87c49/livewire.min.js.map"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETlivewire-dbf87c49-livewire-min-js-map', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETlivewire-dbf87c49-livewire-min-js-map"
                    onclick="tryItOut('GETlivewire-dbf87c49-livewire-min-js-map');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETlivewire-dbf87c49-livewire-min-js-map"
                    onclick="cancelTryOut('GETlivewire-dbf87c49-livewire-min-js-map');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETlivewire-dbf87c49-livewire-min-js-map"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>livewire-dbf87c49/livewire.min.js.map</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETlivewire-dbf87c49-livewire-min-js-map"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETlivewire-dbf87c49-livewire-min-js-map"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETlivewire-dbf87c49-livewire-min-js-map"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="general-GETlivewire-dbf87c49-livewire-csp-min-js-map">GET livewire-dbf87c49/livewire.csp.min.js.map</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETlivewire-dbf87c49-livewire-csp-min-js-map">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/livewire-dbf87c49/livewire.csp.min.js.map" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/livewire-dbf87c49/livewire.csp.min.js.map"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/livewire-dbf87c49/livewire.csp.min.js.map';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETlivewire-dbf87c49-livewire-csp-min-js-map">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">content-type: application/javascript; charset=utf-8
expires: Fri, 23 Apr 2027 07:30:54 GMT
cache-control: max-age=31536000, public
accept-ranges: bytes
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;"></code>
 </pre>
    </span>
<span id="execution-results-GETlivewire-dbf87c49-livewire-csp-min-js-map" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETlivewire-dbf87c49-livewire-csp-min-js-map"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETlivewire-dbf87c49-livewire-csp-min-js-map"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETlivewire-dbf87c49-livewire-csp-min-js-map" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETlivewire-dbf87c49-livewire-csp-min-js-map">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETlivewire-dbf87c49-livewire-csp-min-js-map" data-method="GET"
      data-path="livewire-dbf87c49/livewire.csp.min.js.map"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETlivewire-dbf87c49-livewire-csp-min-js-map', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETlivewire-dbf87c49-livewire-csp-min-js-map"
                    onclick="tryItOut('GETlivewire-dbf87c49-livewire-csp-min-js-map');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETlivewire-dbf87c49-livewire-csp-min-js-map"
                    onclick="cancelTryOut('GETlivewire-dbf87c49-livewire-csp-min-js-map');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETlivewire-dbf87c49-livewire-csp-min-js-map"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>livewire-dbf87c49/livewire.csp.min.js.map</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETlivewire-dbf87c49-livewire-csp-min-js-map"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETlivewire-dbf87c49-livewire-csp-min-js-map"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETlivewire-dbf87c49-livewire-csp-min-js-map"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="general-POSTlivewire-dbf87c49-upload-file">POST livewire-dbf87c49/upload-file</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTlivewire-dbf87c49-upload-file">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/livewire-dbf87c49/upload-file" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/livewire-dbf87c49/upload-file"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/livewire-dbf87c49/upload-file';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTlivewire-dbf87c49-upload-file">
</span>
<span id="execution-results-POSTlivewire-dbf87c49-upload-file" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTlivewire-dbf87c49-upload-file"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTlivewire-dbf87c49-upload-file"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTlivewire-dbf87c49-upload-file" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTlivewire-dbf87c49-upload-file">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTlivewire-dbf87c49-upload-file" data-method="POST"
      data-path="livewire-dbf87c49/upload-file"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTlivewire-dbf87c49-upload-file', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTlivewire-dbf87c49-upload-file"
                    onclick="tryItOut('POSTlivewire-dbf87c49-upload-file');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTlivewire-dbf87c49-upload-file"
                    onclick="cancelTryOut('POSTlivewire-dbf87c49-upload-file');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTlivewire-dbf87c49-upload-file"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>livewire-dbf87c49/upload-file</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTlivewire-dbf87c49-upload-file"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTlivewire-dbf87c49-upload-file"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTlivewire-dbf87c49-upload-file"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="general-GETlivewire-dbf87c49-preview-file--filename-">GET livewire-dbf87c49/preview-file/{filename}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETlivewire-dbf87c49-preview-file--filename-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/livewire-dbf87c49/preview-file/consequatur" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/livewire-dbf87c49/preview-file/consequatur"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/livewire-dbf87c49/preview-file/consequatur';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETlivewire-dbf87c49-preview-file--filename-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6InZUT2F2cUlleXZzaHdIaWpIK1kva2c9PSIsInZhbHVlIjoiMVZYQXJET0UrZW9SY1MxZ0pGQy9GVWVvWDgzeGFJTlkySDlxRzkwMVpZUXF1S0k0cHVTQ2FGVFhEdllJR3ViT2hjV3NYZDRqMGtuMzI4SlVlYjVGUVFKL0I1T2dhSzB5L3BYODlZcnBxMjUzYVJpYm94dC94NnBrTmVFak81TnoiLCJtYWMiOiJhZDNhNjUwN2ZiMTRlZWFmZjc2MDc4YTc1ZDMyOTM1YmM2NjVlZGVlZjlkMzk2YTIyYWVjZmEwYjA0YzU1OTI5IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:54 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6IkVVS1ExdFBNT0NEWXZIdHI0MHRFTVE9PSIsInZhbHVlIjoidDJ1NW1jcm1DS1prMEJWMXlnMTR0RG5HVnIxdTlYaWVaQ05PSmE3WC9qaG5hSzQydDYvSDRpV05kWGZzWE81SGNCRFNNKzBCYS9vLzAvRWluMjdJZUJyYSs4N1hTYmZuZ3oyWGJOc04xZk02eW4wLzRFK2VFTVZkT0R1STJ6YmQiLCJtYWMiOiJmYmU5YzY4MTE2NGRhZTRmZDUwOWVkMDNjYWNhNDRmNTNkOGYzMDgyMjJjMmM5MmFjYjU1ZmM5NzJiNTVmZDgzIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:54 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETlivewire-dbf87c49-preview-file--filename-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETlivewire-dbf87c49-preview-file--filename-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETlivewire-dbf87c49-preview-file--filename-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETlivewire-dbf87c49-preview-file--filename-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETlivewire-dbf87c49-preview-file--filename-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETlivewire-dbf87c49-preview-file--filename-" data-method="GET"
      data-path="livewire-dbf87c49/preview-file/{filename}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETlivewire-dbf87c49-preview-file--filename-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETlivewire-dbf87c49-preview-file--filename-"
                    onclick="tryItOut('GETlivewire-dbf87c49-preview-file--filename-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETlivewire-dbf87c49-preview-file--filename-"
                    onclick="cancelTryOut('GETlivewire-dbf87c49-preview-file--filename-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETlivewire-dbf87c49-preview-file--filename-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>livewire-dbf87c49/preview-file/{filename}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETlivewire-dbf87c49-preview-file--filename-"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETlivewire-dbf87c49-preview-file--filename-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETlivewire-dbf87c49-preview-file--filename-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filename</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filename"                data-endpoint="GETlivewire-dbf87c49-preview-file--filename-"
               value="consequatur"
               data-component="url">
    <br>
<p>Example: <code>consequatur</code></p>
            </div>
                    </form>

                    <h2 id="general-GETlivewire-dbf87c49-js--component--js">GET livewire-dbf87c49/js/{component}.js</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETlivewire-dbf87c49-js--component--js">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/livewire-dbf87c49/js/consequatur.js" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/livewire-dbf87c49/js/consequatur.js"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/livewire-dbf87c49/js/consequatur.js';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETlivewire-dbf87c49-js--component--js">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETlivewire-dbf87c49-js--component--js" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETlivewire-dbf87c49-js--component--js"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETlivewire-dbf87c49-js--component--js"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETlivewire-dbf87c49-js--component--js" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETlivewire-dbf87c49-js--component--js">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETlivewire-dbf87c49-js--component--js" data-method="GET"
      data-path="livewire-dbf87c49/js/{component}.js"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETlivewire-dbf87c49-js--component--js', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETlivewire-dbf87c49-js--component--js"
                    onclick="tryItOut('GETlivewire-dbf87c49-js--component--js');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETlivewire-dbf87c49-js--component--js"
                    onclick="cancelTryOut('GETlivewire-dbf87c49-js--component--js');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETlivewire-dbf87c49-js--component--js"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>livewire-dbf87c49/js/{component}.js</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETlivewire-dbf87c49-js--component--js"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETlivewire-dbf87c49-js--component--js"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETlivewire-dbf87c49-js--component--js"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>component</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="component"                data-endpoint="GETlivewire-dbf87c49-js--component--js"
               value="consequatur"
               data-component="url">
    <br>
<p>Example: <code>consequatur</code></p>
            </div>
                    </form>

                    <h2 id="general-GETlivewire-dbf87c49-css--component--css">GET livewire-dbf87c49/css/{component}.css</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETlivewire-dbf87c49-css--component--css">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/livewire-dbf87c49/css/consequatur.css" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/livewire-dbf87c49/css/consequatur.css"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/livewire-dbf87c49/css/consequatur.css';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETlivewire-dbf87c49-css--component--css">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETlivewire-dbf87c49-css--component--css" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETlivewire-dbf87c49-css--component--css"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETlivewire-dbf87c49-css--component--css"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETlivewire-dbf87c49-css--component--css" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETlivewire-dbf87c49-css--component--css">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETlivewire-dbf87c49-css--component--css" data-method="GET"
      data-path="livewire-dbf87c49/css/{component}.css"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETlivewire-dbf87c49-css--component--css', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETlivewire-dbf87c49-css--component--css"
                    onclick="tryItOut('GETlivewire-dbf87c49-css--component--css');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETlivewire-dbf87c49-css--component--css"
                    onclick="cancelTryOut('GETlivewire-dbf87c49-css--component--css');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETlivewire-dbf87c49-css--component--css"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>livewire-dbf87c49/css/{component}.css</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETlivewire-dbf87c49-css--component--css"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETlivewire-dbf87c49-css--component--css"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETlivewire-dbf87c49-css--component--css"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>component</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="component"                data-endpoint="GETlivewire-dbf87c49-css--component--css"
               value="consequatur"
               data-component="url">
    <br>
<p>Example: <code>consequatur</code></p>
            </div>
                    </form>

                    <h2 id="general-GETlivewire-dbf87c49-css--component--global-css">GET livewire-dbf87c49/css/{component}.global.css</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETlivewire-dbf87c49-css--component--global-css">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/livewire-dbf87c49/css/consequatur.global.css" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/livewire-dbf87c49/css/consequatur.global.css"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/livewire-dbf87c49/css/consequatur.global.css';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETlivewire-dbf87c49-css--component--global-css">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETlivewire-dbf87c49-css--component--global-css" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETlivewire-dbf87c49-css--component--global-css"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETlivewire-dbf87c49-css--component--global-css"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETlivewire-dbf87c49-css--component--global-css" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETlivewire-dbf87c49-css--component--global-css">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETlivewire-dbf87c49-css--component--global-css" data-method="GET"
      data-path="livewire-dbf87c49/css/{component}.global.css"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETlivewire-dbf87c49-css--component--global-css', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETlivewire-dbf87c49-css--component--global-css"
                    onclick="tryItOut('GETlivewire-dbf87c49-css--component--global-css');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETlivewire-dbf87c49-css--component--global-css"
                    onclick="cancelTryOut('GETlivewire-dbf87c49-css--component--global-css');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETlivewire-dbf87c49-css--component--global-css"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>livewire-dbf87c49/css/{component}.global.css</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETlivewire-dbf87c49-css--component--global-css"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETlivewire-dbf87c49-css--component--global-css"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETlivewire-dbf87c49-css--component--global-css"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>component</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="component"                data-endpoint="GETlivewire-dbf87c49-css--component--global-css"
               value="consequatur"
               data-component="url">
    <br>
<p>Example: <code>consequatur</code></p>
            </div>
                    </form>

                    <h2 id="general-POSTresend-webhook">Handle a Resend webhook call.</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTresend-webhook">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/resend/webhook" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/resend/webhook"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/resend/webhook';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTresend-webhook">
</span>
<span id="execution-results-POSTresend-webhook" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTresend-webhook"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTresend-webhook"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTresend-webhook" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTresend-webhook">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTresend-webhook" data-method="POST"
      data-path="resend/webhook"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTresend-webhook', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTresend-webhook"
                    onclick="tryItOut('POSTresend-webhook');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTresend-webhook"
                    onclick="cancelTryOut('POSTresend-webhook');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTresend-webhook"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>resend/webhook</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTresend-webhook"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTresend-webhook"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTresend-webhook"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="general-GETup">GET up</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETup">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/up" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/up"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/up';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETup">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html lang=&quot;en&quot;&gt;
&lt;head&gt;
    &lt;meta charset=&quot;utf-8&quot;&gt;
    &lt;meta name=&quot;viewport&quot; content=&quot;width=device-width, initial-scale=1&quot;&gt;

    &lt;title&gt;Laravel&lt;/title&gt;

    &lt;!-- Fonts --&gt;
    &lt;link rel=&quot;preconnect&quot; href=&quot;https://fonts.bunny.net&quot;&gt;
    &lt;link href=&quot;https://fonts.bunny.net/css?family=figtree:400,600&amp;display=swap&quot; rel=&quot;stylesheet&quot; /&gt;

    &lt;!-- Styles --&gt;
    &lt;script src=&quot;https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4&quot;&gt;&lt;/script&gt;

    &lt;style type=&quot;text/tailwindcss&quot;&gt;
        @theme {
            --font-sans: &#039;Figtree&#039;, &#039;ui-sans-serif&#039;, &#039;system-ui&#039;, &#039;sans-serif&#039;, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;;
        }
    &lt;/style&gt;
&lt;/head&gt;
&lt;body class=&quot;antialiased&quot;&gt;
&lt;div class=&quot;relative flex justify-center items-center min-h-screen bg-gray-100 selection:bg-red-500 selection:text-white&quot;&gt;
    &lt;div class=&quot;w-full sm:w-3/4 xl:w-1/2 mx-auto p-6&quot;&gt;
        &lt;div class=&quot;px-6 py-4 bg-white from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 flex items-center focus:outline focus:outline-2 focus:outline-red-500&quot;&gt;
            &lt;div class=&quot;relative flex h-3 w-3 group &quot;&gt;
                &lt;span class=&quot;animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 group-[.status-down]:bg-red-600 opacity-75&quot;&gt;&lt;/span&gt;
                &lt;span class=&quot;relative inline-flex rounded-full h-3 w-3 bg-green-400 group-[.status-down]:bg-red-600&quot;&gt;&lt;/span&gt;
            &lt;/div&gt;

            &lt;div class=&quot;ml-6&quot;&gt;
                &lt;h2 class=&quot;text-xl font-semibold text-gray-900&quot;&gt;Application up&lt;/h2&gt;

                &lt;p class=&quot;mt-2 text-gray-500 dark:text-gray-400 text-sm leading-relaxed&quot;&gt;
                    HTTP request received.

                                            Response rendered in 1831ms.
                                    &lt;/p&gt;
            &lt;/div&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;
</code>
 </pre>
    </span>
<span id="execution-results-GETup" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETup"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETup"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETup" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETup">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETup" data-method="GET"
      data-path="up"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETup', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETup"
                    onclick="tryItOut('GETup');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETup"
                    onclick="cancelTryOut('GETup');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETup"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>up</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETup"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETup"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETup"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="general-GEThealth">GET health</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GEThealth">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/health" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/health"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/health';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GEThealth">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6Imd6VUlzZTU5ZnpoVDJhNzZCZ2NuR0E9PSIsInZhbHVlIjoiREtOZkV1aEg3a2Y1LzB4YmszM1lTd2gydHVBQnpLRDNUakYyTFhBZGVSMmw5T1NCVjNIMmFtQWt2RHFvV1hvbFcxRjlIbEVabGplcGExY1JnT3JlS09IazUwOUo2aFhTVGJnNHhXUkJZU0pJYjBlK2txZTh3VWdxa1kwR2lCM24iLCJtYWMiOiIxODc5MjdkMjFiZTQ0NDM0ODk2ODkzM2Q0MDQyZTdjNjU5MTkxOWQ1NTAwYzY0N2FmMmNlNTBhMmQ2NmYxY2VjIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6Ik9Nc01hWFRoV3BsR3o3UWNVRklVVmc9PSIsInZhbHVlIjoiWWIvYUp5REoyZjFYbm4vRGRWWVZuSm83d3MzYXkvYmw0R25TMk9tOHh1aGxQdFFab3Z3RmVtYmlUeHZiUjNkdUFuRTh6cmhxMVAzL3BhWEo1V01qTmI1T1g4VmR4VlZMZGIwMGZXSEFYdmRWSzJSU0UwNFlYdnF3VFpmVlB6OVMiLCJtYWMiOiI3ZTUwNjFjODUyNGRjN2I2MTJjYWY4NmU1MTg2ZTI3NTRhN2IzYWYyMGU2OGE0YTU0NjI2ZDgwZWJlNGVmODIxIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;ok&quot;,
    &quot;checks&quot;: {
        &quot;database&quot;: &quot;ok&quot;,
        &quot;redis&quot;: &quot;ok&quot;,
        &quot;queue&quot;: &quot;ok (no pending jobs)&quot;,
        &quot;disk&quot;: &quot;73% free&quot;,
        &quot;cache&quot;: &quot;ok&quot;
    },
    &quot;timestamp&quot;: &quot;2026-04-23T07:30:55+00:00&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GEThealth" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GEThealth"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GEThealth"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GEThealth" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GEThealth">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GEThealth" data-method="GET"
      data-path="health"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GEThealth', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GEThealth"
                    onclick="tryItOut('GEThealth');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GEThealth"
                    onclick="cancelTryOut('GEThealth');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GEThealth"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>health</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GEThealth"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GEThealth"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GEThealth"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="general-GET-">GET /</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GET-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GET-">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6Im0xdS9MeG1XelZlazJUOG9UdUVtM3c9PSIsInZhbHVlIjoiS3NQakJwdzF5NHR1TkpHR1Z0YTlJek9ZRVBETk5UeFZ1anJnVnNvYk16aTdMa3ArQ0p6T0diaGpiLzM4aWVoUlVqRzNmQmRRTFVkT0VOb2x4ZHd4aXJXYnNVUTBTdnp2NGp5TkJWYVFYWGg3TWVmQlRGVFR5RXdOVDZKUmhWK0ciLCJtYWMiOiI3NjdjNjlmZjhkY2Y5M2ExNDEzYjU0MjlmMWQ4Y2ZjYTIxMjA2NWNmODM5N2E1ZjFhYTdjYjcyN2Q3OTAwZDRkIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6ImZUR2s4aDIxNFBNcFNkcnFCYXhZNHc9PSIsInZhbHVlIjoibmsreGlqSkJqcmZqbHlscTlQcVo3WUdmOGpjSjQ2em1abHF1VFZrZDE3UXBEdmtINHBlamV4SFBOcTA2WmJDMW9qZDFreC84QlBxUktXc3NpYmdyUUcyc3kycVpPNnNydFh6OWxNMGwrZHFybnpMK1NucWUxeXpaRVU0TjNIODYiLCJtYWMiOiJmZDY3ZjBjY2EyNzAyMTMwZGEyMWE3NDE5MmY5ZTFkMDUwNDAxYzFhMzRjMjFjN2I1OTVhMTNhNDZjMjA2MGFkIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GET-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GET-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GET-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GET-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GET-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GET-" data-method="GET"
      data-path="/"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GET-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GET-"
                    onclick="tryItOut('GET-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GET-"
                    onclick="cancelTryOut('GET-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GET-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>/</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GET-"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GET-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GET-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="general-POSTmy-tickets--ticket_id--dismiss">POST my-tickets/{ticket_id}/dismiss</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTmy-tickets--ticket_id--dismiss">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/my-tickets/9/dismiss" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/my-tickets/9/dismiss"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/my-tickets/9/dismiss';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTmy-tickets--ticket_id--dismiss">
</span>
<span id="execution-results-POSTmy-tickets--ticket_id--dismiss" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTmy-tickets--ticket_id--dismiss"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTmy-tickets--ticket_id--dismiss"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTmy-tickets--ticket_id--dismiss" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTmy-tickets--ticket_id--dismiss">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTmy-tickets--ticket_id--dismiss" data-method="POST"
      data-path="my-tickets/{ticket_id}/dismiss"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTmy-tickets--ticket_id--dismiss', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTmy-tickets--ticket_id--dismiss"
                    onclick="tryItOut('POSTmy-tickets--ticket_id--dismiss');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTmy-tickets--ticket_id--dismiss"
                    onclick="cancelTryOut('POSTmy-tickets--ticket_id--dismiss');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTmy-tickets--ticket_id--dismiss"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>my-tickets/{ticket_id}/dismiss</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTmy-tickets--ticket_id--dismiss"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTmy-tickets--ticket_id--dismiss"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTmy-tickets--ticket_id--dismiss"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>ticket_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="ticket_id"                data-endpoint="POSTmy-tickets--ticket_id--dismiss"
               value="9"
               data-component="url">
    <br>
<p>The ID of the ticket. Example: <code>9</code></p>
            </div>
                    </form>

                    <h2 id="general-POSTevents--event_id--bookmark">POST events/{event_id}/bookmark</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTevents--event_id--bookmark">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/events/1/bookmark" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/events/1/bookmark"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/events/1/bookmark';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-POSTevents--event_id--bookmark">
</span>
<span id="execution-results-POSTevents--event_id--bookmark" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTevents--event_id--bookmark"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTevents--event_id--bookmark"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTevents--event_id--bookmark" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTevents--event_id--bookmark">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTevents--event_id--bookmark" data-method="POST"
      data-path="events/{event_id}/bookmark"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTevents--event_id--bookmark', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTevents--event_id--bookmark"
                    onclick="tryItOut('POSTevents--event_id--bookmark');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTevents--event_id--bookmark"
                    onclick="cancelTryOut('POSTevents--event_id--bookmark');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTevents--event_id--bookmark"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>events/{event_id}/bookmark</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="POSTevents--event_id--bookmark"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTevents--event_id--bookmark"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTevents--event_id--bookmark"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>event_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="event_id"                data-endpoint="POSTevents--event_id--bookmark"
               value="1"
               data-component="url">
    <br>
<p>The ID of the event. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="general-GETadmin-events--id-">GET admin/events/{id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETadmin-events--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/admin/events/1" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/admin/events/1"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/admin/events/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETadmin-events--id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IjFtVTN3TCt5WVhDTm4zMDBUY1hhOEE9PSIsInZhbHVlIjoiS1JsT0E5QXUvcW4rRG1tanZjcE8rdW1keFBoSTBLZnZucHh6dTZDUW50QVUzTFRSM0ZXQ255VVJFS011dXFtUGpZRDVMbURtQjhFcloxL3owamhMS0pySFYyb3VnOVZiaXczNE5JTllOMXoxTzJQaExyekQ3TGt1V1orb0lEVlUiLCJtYWMiOiIxNjJmMzk3ZGNjZTkxMmE4OWFiNzRmYTM0OWZiZWExYTU3YTY2M2I5YzAyMDIxNGY2OGQ0ZDUyNzFhNzlkYWQ5IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6InAwMVZjTWdCWGlYeVNSVm5vRGRTS3c9PSIsInZhbHVlIjoiRmtxODgwN2xGZGl6V1R5bUdZSDlONHZEbTNrSTF6dXVkUnNsQ0ZwS0RIS2NWY3ZBSCtuTDYxR1FRcjhxQThZZGJmV1hNeWFXLzlYdmh1ZTAreHhuY3Z3WVN4YWU0NFVIYVVZcFNabDVvT2d5ZHZRSTFaVjlGMy9UQlp1NHNOSE0iLCJtYWMiOiIzMTNjODIwNmMxNzhjMmU5MDgwZGE5ZjhlNWFmZTI2OTU3YWRiZWZkYjYwMmFkY2VlNjIxYWQzNzRlNWUzZGJhIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETadmin-events--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETadmin-events--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETadmin-events--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETadmin-events--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETadmin-events--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETadmin-events--id-" data-method="GET"
      data-path="admin/events/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETadmin-events--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETadmin-events--id-"
                    onclick="tryItOut('GETadmin-events--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETadmin-events--id-"
                    onclick="cancelTryOut('GETadmin-events--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETadmin-events--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>admin/events/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETadmin-events--id-"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETadmin-events--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETadmin-events--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETadmin-events--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the event. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="general-GETadmin-finance">GET admin/finance</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETadmin-finance">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/admin/finance" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/admin/finance"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/admin/finance';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETadmin-finance">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6IkNTNzR4cnkzRCswdzNTNGJOSGNpaHc9PSIsInZhbHVlIjoiS3lYS2IxZ25LWEFaVU1rNjBqZmtTOUYyZDNXcGU0YWV4Rkcxa1pBWnYycFZzcE5waVczOG9HY2VVSk0vRTRON0hZenQyUWl2ZFh1YkY2SDE5WXVEckpHbXNkRkdoWjhBSzdwalh5S1Nlbmk0eGhZUjYwOUNBRTBuNVpSdE14dEUiLCJtYWMiOiJmZTQ0NmYwZGI2Yjc3MmMwNDI3MGJjYzBhOWJmNDdkNDAyNjAwOWE0YjViMmZjMWI4ODdjYzBlYzhjOGM3NWRmIiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6ImZNU3gzdkdDRjNFMEsrekNPWTBaaWc9PSIsInZhbHVlIjoiS2hmaE9TQk8yUW12Z3BVK2VIMUpPSHM4ajRFZVBtQi9PNVg2MXcyREJSZ0VkR2xrWXZmcmttU1A4MVpkRU8ycFlLM1B1ZUwxalgxVmQyc3M3bXFyS2sxYWM2QXJsQUdpL2Rtd2ZwcCtWM3JJWUFLaGZYSkpHL2xrdnBQa0N4dGYiLCJtYWMiOiIzOGU5Y2U4MGQ0MGZlM2MwNTE0NGI5MDY3MDgxNjAyZmU3YjA5ZTU0MjY4ZDFkMDQ3MTVhODg2NTc2OTA0ZTg2IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETadmin-finance" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETadmin-finance"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETadmin-finance"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETadmin-finance" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETadmin-finance">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETadmin-finance" data-method="GET"
      data-path="admin/finance"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETadmin-finance', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETadmin-finance"
                    onclick="tryItOut('GETadmin-finance');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETadmin-finance"
                    onclick="cancelTryOut('GETadmin-finance');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETadmin-finance"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>admin/finance</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETadmin-finance"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETadmin-finance"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETadmin-finance"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="general-GETadmin-finance--event_id-">GET admin/finance/{event_id}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETadmin-finance--event_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/admin/finance/1" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/admin/finance/1"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/admin/finance/1';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETadmin-finance--event_id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
set-cookie: XSRF-TOKEN=eyJpdiI6ImxuUTVhZGJJR3diUDYrTWF2K3pKdXc9PSIsInZhbHVlIjoiYzlpeHBnOCtUazNMb2RXZkMxWTRTTkozZWFFSU9nZCtFQis2cGVhT1dqRzdyVWt2TGF4K013NWZIMUYyVmlKWFA2U0VqTGlYUFYvdEkzSmd0b2dvTXNNTDRzZXl4MmFKWnNvYWx4R1BUSGszK25MQUtsVFVON01UMW11bjlZUVkiLCJtYWMiOiI2MjdjNzdhMmM2MTdjMjU3N2QwNDU5YjZlM2QyMGU5NzhjMDEwNWFhZTExY2RhYzY2MWYxNGU1MmQ5ZThiMzM2IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; samesite=lax; laravel-session=eyJpdiI6IkdyQS8xYUhxVUJoT3dHSmprbm93K1E9PSIsInZhbHVlIjoibGFrbXQ1Vm1zMEYvL3RmRjVmSElqeGp6UkFabnl0NUY0b3pTRUh1WkprS0dFcmd4MGlrR21BQVJxdkNKMUZDSTJkeHBYRmxZTjd4NzlOT1FHNGZWaDRkQmUyMmZNYldqMUZrT0NKZFRVSVUwVGp0dmRMSUZnNVNxLzUzeC9kNXgiLCJtYWMiOiJkNWE2NjA2NTY1ZGEwOTMyZDQ2NTcxY2IzMzU2ZDE4ZDEyMmYzMGZhYjBhMDZmOTMyOWI1N2JkYTEwMTJmNDI3IiwidGFnIjoiIn0%3D; expires=Thu, 23 Apr 2026 09:30:55 GMT; Max-Age=7200; path=/; httponly; samesite=lax
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETadmin-finance--event_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETadmin-finance--event_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETadmin-finance--event_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETadmin-finance--event_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETadmin-finance--event_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETadmin-finance--event_id-" data-method="GET"
      data-path="admin/finance/{event_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETadmin-finance--event_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETadmin-finance--event_id-"
                    onclick="tryItOut('GETadmin-finance--event_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETadmin-finance--event_id-"
                    onclick="cancelTryOut('GETadmin-finance--event_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETadmin-finance--event_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>admin/finance/{event_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETadmin-finance--event_id-"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETadmin-finance--event_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETadmin-finance--event_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>event_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="event_id"                data-endpoint="GETadmin-finance--event_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the event. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="general-GETstorage--path-">GET storage/{path}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETstorage--path-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/storage/2UZ5i" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/storage/2UZ5i"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/storage/2UZ5i';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-GETstorage--path-">
            <blockquote>
            <p>Example response (403):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETstorage--path-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETstorage--path-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETstorage--path-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETstorage--path-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETstorage--path-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETstorage--path-" data-method="GET"
      data-path="storage/{path}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETstorage--path-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETstorage--path-"
                    onclick="tryItOut('GETstorage--path-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETstorage--path-"
                    onclick="cancelTryOut('GETstorage--path-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETstorage--path-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>storage/{path}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="GETstorage--path-"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETstorage--path-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETstorage--path-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>path</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="path"                data-endpoint="GETstorage--path-"
               value="2UZ5i"
               data-component="url">
    <br>
<p>Example: <code>2UZ5i</code></p>
            </div>
                    </form>

                    <h2 id="general-PUTstorage--path-">PUT storage/{path}</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PUTstorage--path-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/storage/2UZ5i" \
    --header "Cookie: {YOUR_SESSION_COOKIE}" \
    --header "Accept: application/json" \
    --header "Content-Type: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/storage/2UZ5i"
);

const headers = {
    "Cookie": "{YOUR_SESSION_COOKIE}",
    "Accept": "application/json",
    "Content-Type": "application/json",
};


fetch(url, {
    method: "PUT",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://localhost/storage/2UZ5i';
$response = $client-&gt;put(
    $url,
    [
        'headers' =&gt; [
            'Cookie' =&gt; '{YOUR_SESSION_COOKIE}',
            'Accept' =&gt; 'application/json',
            'Content-Type' =&gt; 'application/json',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>

</span>

<span id="example-responses-PUTstorage--path-">
</span>
<span id="execution-results-PUTstorage--path-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTstorage--path-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTstorage--path-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTstorage--path-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTstorage--path-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTstorage--path-" data-method="PUT"
      data-path="storage/{path}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTstorage--path-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTstorage--path-"
                    onclick="tryItOut('PUTstorage--path-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTstorage--path-"
                    onclick="cancelTryOut('PUTstorage--path-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTstorage--path-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>storage/{path}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Cookie</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Cookie" class="auth-value"               data-endpoint="PUTstorage--path-"
               value="{YOUR_SESSION_COOKIE}"
               data-component="header">
    <br>
<p>Example: <code>{YOUR_SESSION_COOKIE}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTstorage--path-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTstorage--path-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>path</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="path"                data-endpoint="PUTstorage--path-"
               value="2UZ5i"
               data-component="url">
    <br>
<p>Example: <code>2UZ5i</code></p>
            </div>
                    </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                                        <button type="button" class="lang-button" data-language-name="php">php</button>
                            </div>
            </div>
</div>
</body>
</html>
