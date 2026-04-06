@extends('layouts.admin')

@php
    $defaultTicketCategories = old('ticket_categories', [
        ['name' => 'VIP', 'price' => '', 'ticket_count' => '', 'description' => ''],
        ['name' => 'Ordinary', 'price' => '', 'ticket_count' => '', 'description' => ''],
    ]);
@endphp

@section('title', 'Create Event')
@section('heading', 'Create Event')

@section('content')
    <section class="form-panel">
        <div class="panel-copy">
            <h2>New event details</h2>
            <p>Fill in the event information below. The record will be saved in the database and appear on the public events page immediately.</p>
        </div>

        <form method="POST" action="{{ route('admin.events.store') }}" class="event-form" enctype="multipart/form-data">
            @csrf
            <div class="field-grid">
                <label class="field"><span>Event title</span><input type="text" name="title" value="{{ old('title') }}" required>@error('title') <small>{{ $message }}</small> @enderror</label>
                <label class="field"><span>Category</span><select name="category_id" required><option value="">Select a category</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select>@error('category_id') <small>{{ $message }}</small> @enderror</label>
                <label class="field"><span>Venue</span><input type="text" name="venue" value="{{ old('venue') }}" required>@error('venue') <small>{{ $message }}</small> @enderror</label>
                <label class="field"><span>City</span><input type="text" name="city" value="{{ old('city') }}" required>@error('city') <small>{{ $message }}</small> @enderror</label>
                <label class="field"><span>Country</span><input type="text" name="country" value="{{ old('country', 'Uganda') }}" required>@error('country') <small>{{ $message }}</small> @enderror</label>
                <label class="field"><span>Status</span><select name="status" required><option value="draft" @selected(old('status') === 'draft')>Draft</option><option value="published" @selected(old('status') === 'published')>Published</option><option value="cancelled" @selected(old('status') === 'cancelled')>Cancelled</option></select>@error('status') <small>{{ $message }}</small> @enderror</label>
                <label class="field"><span>Start date & time</span><input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required>@error('starts_at') <small>{{ $message }}</small> @enderror</label>
                <label class="field"><span>End date & time</span><input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}">@error('ends_at') <small>{{ $message }}</small> @enderror</label>
                <label class="field field-full"><span>Event image from your computer</span><input type="file" name="image_file" accept="image/*">@error('image_file') <small>{{ $message }}</small> @enderror</label>
                <label class="field field-full"><span>Description</span><textarea name="description" rows="6" required>{{ old('description') }}</textarea>@error('description') <small>{{ $message }}</small> @enderror</label>
            </div>

            <div class="ticket-tier-panel">
                <div class="ticket-tier-head">
                    <div>
                        <h3>Ticket categories</h3>
                        <p>Create as many ticket types as you need, for example VIP 300 tickets and Ordinary 50 tickets.</p>
                    </div>
                    <button type="button" class="tier-add-btn" id="add-ticket-tier">Add ticket category</button>
                </div>

                @error('ticket_categories') <small class="block-error">{{ $message }}</small> @enderror

                <div
                    id="ticket-tier-list"
                    class="ticket-tier-list"
                    data-old='@json($defaultTicketCategories)'
                ></div>
            </div>

            <label class="checkbox-row">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured'))>
                <span>Feature this event on the home page</span>
            </label>

            <button type="submit" class="submit-btn">Save event</button>
        </form>

        @if ($categories->isEmpty())
            <p class="helper-note">No categories exist yet. <a href="{{ route('admin.categories.index') }}">Create a category first</a> so you can assign it to an event.</p>
        @endif

        <p class="helper-note">Use the file picker above to upload an image directly from your laptop or machine.</p>
    </section>

    <style>
        .form-panel { background: #fff; border-radius: 24px; padding: 1.5rem; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.07); }
        .panel-copy h2 { margin: 0; }
        .panel-copy p { color: #667085; max-width: 62ch; line-height: 1.6; }
        .event-form { margin-top: 1.4rem; }
        .field-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .field { display: grid; gap: 0.45rem; color: #344054; font-weight: 600; }
        .field-full { grid-column: 1 / -1; }
        .field input, .field select, .field textarea { width: 100%; border: 1px solid #d0d5dd; border-radius: 16px; padding: 0.9rem 1rem; font: inherit; color: #101828; background: #fff; }
        .field small { color: #b42318; }
        .ticket-tier-panel { margin-top: 1.5rem; padding: 1.2rem; border-radius: 22px; background: #f8fafc; border: 1px solid #eaecf0; }
        .ticket-tier-head { display: flex; justify-content: space-between; align-items: start; gap: 1rem; margin-bottom: 1rem; }
        .ticket-tier-head h3 { margin: 0; }
        .ticket-tier-head p { margin: 0.35rem 0 0; color: #667085; line-height: 1.6; }
        .ticket-tier-list { display: grid; gap: 1rem; }
        .ticket-tier-card { padding: 1rem; border-radius: 18px; background: #fff; border: 1px solid #dfe3ea; }
        .ticket-tier-card-head { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 0.8rem; }
        .ticket-tier-card-head strong { font-size: 0.95rem; }
        .ticket-tier-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.9rem; }
        .tier-add-btn, .tier-remove-btn { border: 0; border-radius: 999px; padding: 0.75rem 1rem; font: inherit; font-weight: 700; cursor: pointer; }
        .tier-add-btn { background: #101828; color: #fff; }
        .tier-remove-btn { background: #fee2e2; color: #b91c1c; }
        .block-error { display: block; margin-bottom: 0.8rem; color: #b42318; }
        .checkbox-row { display: inline-flex; align-items: center; gap: 0.7rem; margin-top: 1.2rem; color: #344054; font-weight: 600; }
        .submit-btn { margin-top: 1.5rem; border: 0; border-radius: 999px; padding: 0.95rem 1.2rem; background: linear-gradient(135deg, #f15a24, #dc2626); color: #fff; font: inherit; font-weight: 700; cursor: pointer; }
        .helper-note { margin-top: 1rem; color: #667085; }
        .helper-note a { color: #b45309; font-weight: 700; text-decoration: none; }
        @media (max-width: 860px) { .field-grid, .ticket-tier-grid { grid-template-columns: 1fr; } .ticket-tier-head, .ticket-tier-card-head { flex-direction: column; align-items: start; } }
    </style>

    <script>
        (() => {
            const tierList = document.getElementById('ticket-tier-list');
            const addButton = document.getElementById('add-ticket-tier');

            if (!tierList || !addButton) return;

            const oldTiers = JSON.parse(tierList.dataset.old || '[]');

            const buildTierCard = (tier = {}, index = 0) => {
                const card = document.createElement('div');
                card.className = 'ticket-tier-card';
                card.innerHTML = `
                    <div class="ticket-tier-card-head">
                        <strong>Ticket category ${index + 1}</strong>
                        <button type="button" class="tier-remove-btn">Remove</button>
                    </div>
                    <div class="ticket-tier-grid">
                        <label class="field">
                            <span>Name</span>
                            <input type="text" name="ticket_categories[${index}][name]" value="${tier.name ?? ''}" placeholder="VIP" required>
                        </label>
                        <label class="field">
                            <span>Price</span>
                            <input type="number" step="0.01" min="0" name="ticket_categories[${index}][price]" value="${tier.price ?? ''}" required>
                        </label>
                        <label class="field">
                            <span>Number of tickets</span>
                            <input type="number" min="1" name="ticket_categories[${index}][ticket_count]" value="${tier.ticket_count ?? ''}" required>
                        </label>
                        <label class="field" style="grid-column: 1 / -1;">
                            <span>Description</span>
                            <input type="text" name="ticket_categories[${index}][description]" value="${tier.description ?? ''}" placeholder="Front row seating, lounge access, regular gate, etc.">
                        </label>
                    </div>
                `;

                card.querySelector('.tier-remove-btn').addEventListener('click', () => {
                    card.remove();
                    syncIndices();
                });

                return card;
            };

            const syncIndices = () => {
                [...tierList.children].forEach((card, index) => {
                    card.querySelector('strong').textContent = `Ticket category ${index + 1}`;
                    card.querySelectorAll('input').forEach((input) => {
                        input.name = input.name.replace(/ticket_categories\[\d+\]/, `ticket_categories[${index}]`);
                    });
                });
            };

            const addTier = (tier = {}) => {
                tierList.appendChild(buildTierCard(tier, tierList.children.length));
                syncIndices();
            };

            addButton.addEventListener('click', () => addTier());

            oldTiers.forEach((tier) => addTier(tier));
        })();
    </script>
@endsection
