@extends('layouts.admin')

@php
    $defaultTicketCategories = old('ticket_categories', [
        ['name' => 'VIP', 'price' => '', 'ticket_count' => '', 'description' => ''],
        ['name' => 'Ordinary', 'price' => '', 'ticket_count' => '', 'description' => ''],
    ]);
    $defaultArtists = old('artists', [
        ['name' => ''],
    ]);
@endphp

@section('title', 'Create Event')
@section('heading', 'Create Event')

@section('content')
    <div class="create-event-container">
        <div class="form-header">
            <h1>New Event</h1>
            <p>Fill in the details below. The event will be saved and visible on the public page once published.</p>
        </div>

        <form method="POST" action="{{ route('admin.events.store') }}" class="event-form" enctype="multipart/form-data">
            @csrf

            <!-- Basic Information -->
            <div class="form-card">
                <div class="card-header">
                    <h2>Event Details</h2>
                </div>
                <div class="field-grid">
                    <div class="field">
                        <label>Event Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="Summer Music Festival 2026">
                        @error('title') <small class="error">{{ $message }}</small> @enderror
                    </div>

                    <div class="field">
                        <label>Category</label>
                        <select name="category_id" required>
                            <option value="">Select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <small class="error">{{ $message }}</small> @enderror
                    </div>

                    <div class="field">
                        <label>Venue</label>
                        <input type="text" name="venue" value="{{ old('venue') }}" required placeholder="National Theatre">
                        @error('venue') <small class="error">{{ $message }}</small> @enderror
                    </div>

                    <div class="field">
                        <label>City</label>
                        <input type="text" name="city" value="{{ old('city') }}" required>
                        @error('city') <small class="error">{{ $message }}</small> @enderror
                    </div>

                    <div class="field">
                        <label>Country</label>
                        <input type="text" name="country" value="{{ old('country', 'Uganda') }}" required>
                        @error('country') <small class="error">{{ $message }}</small> @enderror
                    </div>

                    <div class="field">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="draft" @selected(old('status') === 'draft')>Draft</option>
                            <option value="published" @selected(old('status') === 'published')>Published</option>
                            <option value="cancelled" @selected(old('status') === 'cancelled')>Cancelled</option>
                        </select>
                        @error('status') <small class="error">{{ $message }}</small> @enderror
                    </div>

                    <div class="field">
                        <label>Start Date & Time</label>
                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required>
                        @error('starts_at') <small class="error">{{ $message }}</small> @enderror
                    </div>

                    <div class="field">
                        <label>End Date & Time</label>
                        <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}">
                        @error('ends_at') <small class="error">{{ $message }}</small> @enderror
                    </div>

                    <div class="field field-full">
                        <label>Cover Image</label>
                        <div class="file-upload">
                            <input type="file" name="image_file" accept="image/*" id="image-upload">
                            <label for="image-upload" class="upload-label">
                                <span>Choose image or drag & drop</span>
                                <small>PNG, JPG up to 10MB</small>
                            </label>
                        </div>
                        @error('image_file') <small class="error">{{ $message }}</small> @enderror
                    </div>

                    <div class="field field-full">
                        <label>Description</label>
                        <textarea name="description" rows="7" required placeholder="Tell attendees what to expect...">{{ old('description') }}</textarea>
                        @error('description') <small class="error">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            <!-- Ticket Categories -->
            <div class="form-card">
                <div class="card-header">
                    <div>
                        <h2>Ticket Categories</h2>
                        <p>Create different ticket types with prices and quantities.</p>
                    </div>
                    <button type="button" class="add-btn" id="add-ticket-tier">+ Add Category</button>
                </div>

                @error('ticket_categories') <small class="block-error">{{ $message }}</small> @enderror

                <div id="ticket-tier-list" class="dynamic-list" data-old='@json($defaultTicketCategories)'></div>
            </div>

            <!-- Artists -->
            <div class="form-card">
                <div class="card-header">
                    <div>
                        <h2>Artists / Performers</h2>
                        <p>Optional — only for music, comedy, or performance events.</p>
                    </div>
                    <button type="button" class="add-btn" id="add-artist-row">+ Add Artist</button>
                </div>

                <div id="artist-list" class="dynamic-list" data-old='@json($defaultArtists)'></div>
            </div>

            <!-- Options -->
            <div class="form-card options-card">
                <label class="checkbox-row">
                    <input type="checkbox" name="is_free" value="1" @checked(old('is_free')) id="is-free-event">
                    <span>This is a <strong>free event</strong></span>
                </label>

                <label class="checkbox-row">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured'))>
                    <span>Feature on homepage</span>
                </label>
            </div>

            <button type="submit" class="submit-btn">Create Event</button>
        </form>

        @if ($categories->isEmpty())
            <p class="helper-note">No categories yet. <a href="{{ route('admin.categories.index') }}">Create one first</a>.</p>
        @endif
    </div>

    <style>
        :root {
            --primary: #f15a24;
            --primary-dark: #dc2626;
        }

        .create-event-container {
            max-width: 920px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-header h1 {
            font-size: 2.25rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 0.5rem;
        }

        .form-header p {
            color: #64748b;
            font-size: 1.05rem;
            max-width: 65ch;
        }

        .form-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #f1f5f9;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.75rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-header h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .card-header p {
            color: #64748b;
            margin: 0.25rem 0 0;
            font-size: 0.95rem;
        }

        .field-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .field label {
            font-weight: 600;
            color: #334155;
            font-size: 0.95rem;
        }

        .field input,
        .field select,
        .field textarea {
            padding: 0.95rem 1.25rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: #fff;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(241, 90, 36, 0.12);
            outline: none;
        }

        .field-full {
            grid-column: 1 / -1;
        }

        .file-upload {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: all 0.2s;
        }

        .file-upload:hover {
            border-color: var(--primary);
            background: #fffaf5;
        }

        .upload-label {
            cursor: pointer;
            display: block;
        }

        .upload-label span {
            font-weight: 600;
            color: #0f172a;
            display: block;
            margin-bottom: 0.25rem;
        }

        .dynamic-list {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .tier-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 1.5rem;
            position: relative;
        }

        .tier-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .tier-card-header strong {
            font-size: 1.05rem;
            color: #0f172a;
        }

        .tier-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
        }

        .add-btn {
            background: #0f172a;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .add-btn:hover {
            background: #1e2937;
            transform: translateY(-1px);
        }

        .remove-btn {
            background: #fee2e2;
            color: #ef4444;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .options-card {
            padding: 1.75rem;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 1rem;
            cursor: pointer;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 1.1rem;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 9999px;
            cursor: pointer;
            margin-top: 1rem;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(241, 90, 36, 0.25);
        }

        .error {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .block-error {
            color: #ef4444;
            margin: 0.5rem 0 1rem;
            display: block;
        }

        .helper-note {
            text-align: center;
            color: #64748b;
            margin-top: 2rem;
        }

        .helper-note a {
            color: #f59e0b;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .field-grid,
            .tier-grid {
                grid-template-columns: 1fr;
            }
            .form-card {
                padding: 1.5rem;
            }
        }
    </style>

    <script>
        (() => {
            // Ticket Tiers
            const tierList = document.getElementById('ticket-tier-list');
            const addTierBtn = document.getElementById('add-ticket-tier');
            const oldTiers = JSON.parse(tierList?.dataset.old || '[]');

            const createTierCard = (tier = {}, idx = 0) => {
                const card = document.createElement('div');
                card.className = 'tier-card';
                card.innerHTML = `
                    <div class="tier-card-header">
                        <strong>Ticket Category ${idx + 1}</strong>
                        <button type="button" class="remove-btn">Remove</button>
                    </div>
                    <div class="tier-grid">
                        <div class="field">
                            <label>Name</label>
                            <input type="text" name="ticket_categories[${idx}][name]" value="${tier.name || ''}" placeholder="VIP" required>
                        </div>
                        <div class="field">
                            <label>Price (UGX)</label>
                            <input type="number" step="0.01" min="0" name="ticket_categories[${idx}][price]" value="${tier.price || ''}" required class="price-input">
                        </div>
                        <div class="field">
                            <label>Number of Tickets</label>
                            <input type="number" min="1" name="ticket_categories[${idx}][ticket_count]" value="${tier.ticket_count || ''}" required>
                        </div>
                        <div class="field field-full">
                            <label>Description (optional)</label>
                            <input type="text" name="ticket_categories[${idx}][description]" value="${tier.description || ''}" placeholder="Best seats, lounge access...">
                        </div>
                    </div>
                `;

                card.querySelector('.remove-btn').addEventListener('click', () => {
                    card.remove();
                    reindexTiers();
                });

                return card;
            };

            const reindexTiers = () => {
                Array.from(tierList.children).forEach((card, i) => {
                    card.querySelector('strong').textContent = `Ticket Category ${i + 1}`;
                    card.querySelectorAll('input').forEach(input => {
                        input.name = input.name.replace(/ticket_categories\[\d+\]/, `ticket_categories[${i}]`);
                    });
                });
            };

            const addTier = (data = {}) => {
                tierList.appendChild(createTierCard(data, tierList.children.length));
                reindexTiers();
            };

            addTierBtn.addEventListener('click', () => addTier());
            oldTiers.forEach(tier => addTier(tier));

            // Artists
            const artistList = document.getElementById('artist-list');
            const addArtistBtn = document.getElementById('add-artist-row');
            const oldArtists = JSON.parse(artistList?.dataset.old || '[]');

            const createArtistCard = (artist = {}, idx = 0) => {
                const card = document.createElement('div');
                card.className = 'tier-card';
                card.innerHTML = `
                    <div class="tier-card-header">
                        <strong>Artist ${idx + 1}</strong>
                        <button type="button" class="remove-btn">Remove</button>
                    </div>
                    <div class="field">
                        <label>Artist Name</label>
                        <input type="text" name="artists[${idx}][name]" value="${artist.name || ''}" placeholder="e.g. Bobi Wine">
                    </div>
                `;

                card.querySelector('.remove-btn').addEventListener('click', () => {
                    card.remove();
                    reindexArtists();
                });

                return card;
            };

            const reindexArtists = () => {
                Array.from(artistList.children).forEach((card, i) => {
                    card.querySelector('strong').textContent = `Artist ${i + 1}`;
                    card.querySelectorAll('input').forEach(input => {
                        input.name = input.name.replace(/artists\[\d+\]/, `artists[${i}]`);
                    });
                });
            };

            const addArtist = (data = {}) => {
                artistList.appendChild(createArtistCard(data, artistList.children.length));
                reindexArtists();
            };

            addArtistBtn.addEventListener('click', () => addArtist());
            oldArtists.forEach(artist => addArtist(artist));

            // Free event logic
            const freeCheckbox = document.getElementById('is-free-event');
            const syncFree = () => {
                const isFree = freeCheckbox?.checked;
                document.querySelectorAll('.price-input').forEach(input => {
                    if (isFree) {
                        input.value = '0';
                        input.readOnly = true;
                    } else {
                        input.readOnly = false;
                    }
                });
            };

            freeCheckbox?.addEventListener('change', syncFree);
            setTimeout(syncFree, 100); // initial sync
        })();
    </script>
@endsection