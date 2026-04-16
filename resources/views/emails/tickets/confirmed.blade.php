@component('mail::message')
# Payment Confirmed

Your ticket has been confirmed for **{{ $ticket->event?->title ?? 'WADO Event' }}**.

@component('mail::panel')
**Ticket Code:** {{ $ticket->ticket_code }}  
**Holder:** {{ $ticket->holder_name ?: ($ticket->user?->name ?? 'N/A') }}  
**Date:** {{ $ticket->event?->starts_at?->format('d M Y H:i') ?? 'TBD' }}  
**Venue:** {{ trim((string) (($ticket->event?->venue ?? '') . ', ' . ($ticket->event?->city ?? ''))) }}
@endcomponent

@if ($qrCodeDataUri)
<div style="text-align:center; margin:16px 0;">
    <img src="{{ $qrCodeDataUri }}" alt="Ticket QR" style="max-width:220px; height:auto;" />
</div>
@endif

@if ($ticketUrl)
@component('mail::button', ['url' => $ticketUrl])
View Your Ticket
@endcomponent
@endif

Keep this email and ticket code for gate verification.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
