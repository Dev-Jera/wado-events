# Introduction

Complete reference for all WADO Events Tickets endpoints: authentication, event browsing, ticket checkout, gate scanning, walk-in sales, payment webhooks, and admin operations.

<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>

    This documentation covers every HTTP endpoint in the WADO Events Tickets platform.

    **Authentication:** Most endpoints require an active browser session (cookie-based). Log in at `/login` first, then use the Try It Out buttons — your session carries over automatically.

    **Webhook endpoints** (e.g. MarzPay) are called by external payment providers, not by the browser.

    **Admin endpoints** require the `super_admin` or `admin` role.

    **Gate portal / scan endpoints** require the `gate_agent` role assigned to the relevant event.

