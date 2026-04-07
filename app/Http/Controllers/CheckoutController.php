<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function create(Request $request, Event $event)
    {
        $event->load('ticketCategories');

        abort_if($event->ticketCategories->isEmpty(), 404);

        $prefill = collect($request->session()->pull("checkout_prefill.{$event->id}", []));
        $selectedCategoryId = (int) ($request->integer('ticket_category') ?: $prefill->get('ticket_category_id', 0));
        $selectedPaymentProvider = $request->string('payment_provider')->toString() ?: (string) $prefill->get('payment_provider', '');
        $selectedQuantity = (int) ($prefill->get('quantity', 1));

        return view('pages.checkout.create', [
            'event' => $event,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedPaymentProvider' => $selectedPaymentProvider,
            'selectedQuantity' => max($selectedQuantity, 1),
        ]);
    }

    public function store(Request $request, Event $event)
    {
        $event->load('ticketCategories');
        $user = $request->user();

        $data = $request->validate([
            'ticket_category_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'payment_provider' => ['nullable', 'in:mtn,airtel'],
        ]);

        $ticketCategory = $event->ticketCategories
            ->firstWhere('id', (int) $data['ticket_category_id']);

        abort_unless($ticketCategory instanceof TicketCategory, 404);

        $quantity = (int) $data['quantity'];
        $unitPrice = (float) $ticketCategory->price;
        $isFree = $unitPrice <= 0;

        if (! $isFree && blank($data['payment_provider'] ?? null)) {
            return back()
                ->withInput()
                ->withErrors([
                    'payment_provider' => 'Choose MTN or Airtel to continue.',
                ]);
        }

        if (! $user) {
            $request->session()->put('checkout_redirect', route('checkout.create', [
                'event' => $event,
                'ticket_category' => $ticketCategory->id,
                'payment_provider' => $data['payment_provider'] ?? null,
            ]));
            $request->session()->put("checkout_prefill.{$event->id}", [
                'ticket_category_id' => $ticketCategory->id,
                'quantity' => $quantity,
                'payment_provider' => $data['payment_provider'] ?? null,
            ]);
            $request->session()->put('url.intended', route('checkout.create', ['event' => $event]));

            return redirect()
                ->route('login')
                ->with('warning', 'You must log in first to complete your ticket purchase.');
        }

        $ticket = DB::transaction(function () use ($data, $event, $isFree, $quantity, $ticketCategory, $unitPrice, $user) {
            $lockedCategory = TicketCategory::query()->findOrFail($ticketCategory->id);
            $lockedEvent = Event::query()->findOrFail($event->id);

            if ($lockedCategory->tickets_remaining < $quantity || $lockedEvent->tickets_available < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Not enough tickets remain for that selection.',
                ]);
            }

            $lockedCategory->decrement('tickets_remaining', $quantity);
            $lockedEvent->decrement('tickets_available', $quantity);

            return Ticket::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'ticket_category_id' => $lockedCategory->id,
                'ticket_code' => 'WADO-' . Str::upper(Str::random(10)),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $unitPrice * $quantity,
                'payment_provider' => $isFree ? 'free' : $data['payment_provider'],
                'status' => 'confirmed',
                'purchased_at' => now(),
            ]);
        });

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket purchase completed successfully.');
    }
}
