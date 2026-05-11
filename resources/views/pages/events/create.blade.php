@extends('layouts.app')

@section('content')

<div class="hec-page">
    {{-- ── Hero ── --}}
    <section class="hec-hero">
        <div class="hec-hero-inner">
            <h1 class="hec-heading">Create Your Event</h1>
            <p class="hec-sub">Fill in your event details below. Once you submit, our team will review and approve your event, then it will go live for guests to purchase tickets.</p>
        </div>
    </section>

    {{-- ── Form ── --}}
    <section class="hec-form-section">
        <div class="hec-shell">
            <form action="{{ route('host-event.store') }}" method="POST" enctype="multipart/form-data" class="hec-form">
                @csrf
                
                {{-- Tabs Navigation --}}
                <div class="hec-tabs">
                    <button type="button" class="hec-tab hec-tab--active" data-tab="event-details">
                        <span class="hec-tab-icon">📋</span>
                        Event Details
                    </button>
                    <button type="button" class="hec-tab" data-tab="cover-image">
                        <span class="hec-tab-icon">🖼️</span>
                        Cover Image
                    </button>
                    <button type="button" class="hec-tab" data-tab="ticket-categories">
                        <span class="hec-tab-icon">🎫</span>
                        Ticket Categories
                    </button>
                    <button type="button" class="hec-tab" data-tab="settings">
                        <span class="hec-tab-icon">⚙️</span>
                        Settings
                    </button>
                </div>

                {{-- Tab 1: Event Details --}}
                <div class="hec-tab-panel hec-tab-panel--active" data-tab-panel="event-details">
                    <div class="hec-section">
                        <h3 class="hec-section-title">Basic Information</h3>
                        
                        <div class="hec-form-group">
                            <label for="title" class="hec-label">Event Title <span class="hec-required">*</span></label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                class="hec-input @error('title') is-invalid @enderror"
                                value="{{ old('title') }}"
                                placeholder="e.g. Live Music Night"
                                required
                            >
                            @error('title')
                                <span class="hec-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="hec-form-row">
                            <div class="hec-form-group">
                                <label for="category_id" class="hec-label">Category <span class="hec-required">*</span></label>
                                <div class="hec-select-wrapper">
                                    <select 
                                        id="category_id" 
                                        name="category_id" 
                                        class="hec-select @error('category_id') is-invalid @enderror"
                                        required
                                    >
                                        <option value="">Select a category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('category_id')
                                    <span class="hec-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="hec-form-group">
                                <label class="hec-label">
                                    <input 
                                        type="checkbox" 
                                        id="create_category" 
                                        class="hec-checkbox"
                                    >
                                    <span class="hec-checkbox-label">Create new category</span>
                                </label>
                                <div id="new-category-input" style="display: none; margin-top: 0.75rem;">
                                    <input 
                                        type="text" 
                                        id="new_category_name" 
                                        class="hec-input"
                                        placeholder="Enter new category name"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="hec-form-row">
                            <div class="hec-form-group">
                                <label for="venue" class="hec-label">Venue <span class="hec-required">*</span></label>
                                <input 
                                    type="text" 
                                    id="venue" 
                                    name="venue" 
                                    class="hec-input @error('venue') is-invalid @enderror"
                                    value="{{ old('venue') }}"
                                    placeholder="e.g. Kampala Serena Hotel"
                                    required
                                >
                                @error('venue')
                                    <span class="hec-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="hec-form-group">
                                <label for="city" class="hec-label">City <span class="hec-required">*</span></label>
                                <input 
                                    type="text" 
                                    id="city" 
                                    name="city" 
                                    class="hec-input @error('city') is-invalid @enderror"
                                    value="{{ old('city') }}"
                                    placeholder="e.g. Kampala"
                                    required
                                >
                                @error('city')
                                    <span class="hec-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="hec-form-group">
                                <label for="country" class="hec-label">Country <span class="hec-required">*</span></label>
                                <input 
                                    type="text" 
                                    id="country" 
                                    name="country" 
                                    class="hec-input @error('country') is-invalid @enderror"
                                    value="{{ old('country', 'Uganda') }}"
                                    placeholder="e.g. Uganda"
                                    required
                                >
                                @error('country')
                                    <span class="hec-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="hec-form-row">
                            <div class="hec-form-group">
                                <label for="starts_at" class="hec-label">Starts At <span class="hec-required">*</span></label>
                                <input 
                                    type="datetime-local" 
                                    id="starts_at" 
                                    name="starts_at" 
                                    class="hec-input @error('starts_at') is-invalid @enderror"
                                    value="{{ old('starts_at') }}"
                                    required
                                >
                                @error('starts_at')
                                    <span class="hec-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="hec-form-group">
                                <label for="ends_at" class="hec-label">Ends At</label>
                                <input 
                                    type="datetime-local" 
                                    id="ends_at" 
                                    name="ends_at" 
                                    class="hec-input @error('ends_at') is-invalid @enderror"
                                    value="{{ old('ends_at') }}"
                                >
                                @error('ends_at')
                                    <span class="hec-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="hec-form-group">
                            <label for="description" class="hec-label">About This Event <span class="hec-required">*</span></label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="hec-textarea @error('description') is-invalid @enderror"
                                placeholder="Describe your event…"
                                rows="6"
                                required
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <span class="hec-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Tab 2: Cover Image --}}
                <div class="hec-tab-panel" data-tab-panel="cover-image">
                    <div class="hec-section">
                        <h3 class="hec-section-title">Cover Image</h3>
                        <p class="hec-section-subtitle">Upload an image (1200×630px recommended). Supports JPG, PNG, WebP.</p>

                        <div class="hec-form-group">
                            <div class="hec-file-upload">
                                <input 
                                    type="file" 
                                    id="image_url" 
                                    name="image_url" 
                                    class="hec-file-input"
                                    accept="image/jpeg,image/png,image/webp"
                                >
                                <label for="image_url" class="hec-file-label">
                                    <div class="hec-file-icon">📸</div>
                                    <span class="hec-file-text">Click to upload or drag and drop</span>
                                    <span class="hec-file-hint">JPG, PNG, WebP up to 5MB</span>
                                </label>
                                <div id="image-preview" class="hec-image-preview" style="display: none;">
                                    <img id="preview-img" src="" alt="Preview">
                                    <button type="button" class="hec-image-remove">✕</button>
                                </div>
                            </div>
                            @error('image_url')
                                <span class="hec-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Tab 3: Ticket Categories --}}
                <div class="hec-tab-panel" data-tab-panel="ticket-categories">
                    <div class="hec-section">
                        <h3 class="hec-section-title">Ticket Categories</h3>

                        <div class="hec-toggle-group">
                            <label class="hec-toggle">
                                <input 
                                    type="checkbox" 
                                    id="is_free" 
                                    name="is_free" 
                                    class="hec-toggle-input"
                                    @checked(old('is_free'))
                                >
                                <span class="hec-toggle-slider"></span>
                                <span class="hec-toggle-label">Free event — no payment required</span>
                            </label>
                        </div>

                        <div id="ticket-categories-container" class="hec-repeater">
                            <!-- Ticket categories will be added here by JavaScript -->
                        </div>

                        <button type="button" id="add-ticket-category" class="hec-btn-secondary">
                            + Add Ticket Category
                        </button>
                        @error('ticket_categories')
                            <span class="hec-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Tab 4: Settings --}}
                <div class="hec-tab-panel" data-tab-panel="settings">
                    <div class="hec-section">
                        <h3 class="hec-section-title">Verification & Re-entry Settings</h3>
                        <p class="hec-section-subtitle">Choose who handles gate verification and configure attendee re-entry.</p>

                        <div class="hec-form-group">
                            <label for="verification_mode" class="hec-label">Gate Verification Mode <span class="hec-required">*</span></label>
                            <div class="hec-select-wrapper">
                                <select 
                                    id="verification_mode"
                                    name="verification_mode"
                                    class="hec-select @error('verification_mode') is-invalid @enderror"
                                    required
                                >
                                    <option value="wado_managed" @selected(old('verification_mode', 'wado_managed') === 'wado_managed')>
                                        WADO-managed verification (recommended)
                                    </option>
                                    <option value="self_managed" @selected(old('verification_mode') === 'self_managed')>
                                        Self-managed verification (I will manage agents)
                                    </option>
                                </select>
                            </div>
                            <span class="hec-help-text">You can update this later from your event dashboard.</span>
                            @error('verification_mode')
                                <span class="hec-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="hec-toggle-group">
                            <label class="hec-toggle">
                                <input 
                                    type="checkbox" 
                                    id="reentry_allowed" 
                                    name="reentry_allowed" 
                                    class="hec-toggle-input"
                                    @checked(old('reentry_allowed'))
                                >
                                <span class="hec-toggle-slider"></span>
                                <span class="hec-toggle-label">Allow re-entry</span>
                            </label>
                        </div>

                        <div id="reentry-options" style="display: none; margin-top: 1.5rem;">
                            <div class="hec-form-row">
                                <div class="hec-form-group">
                                    <label for="reentry_limit" class="hec-label">Max Re-entries Per Ticket</label>
                                    <input 
                                        type="number" 
                                        id="reentry_limit" 
                                        name="reentry_limit" 
                                        class="hec-input"
                                        value="{{ old('reentry_limit', 1) }}"
                                        min="1"
                                    >
                                    <span class="hec-help-text">How many times a ticket can re-enter after the first entry. 1 = one re-entry allowed.</span>
                                </div>

                                <div class="hec-form-group">
                                    <label for="reentry_cooldown_minutes" class="hec-label">Cooldown After Exit (Minutes)</label>
                                    <input 
                                        type="number" 
                                        id="reentry_cooldown_minutes" 
                                        name="reentry_cooldown_minutes" 
                                        class="hec-input"
                                        value="{{ old('reentry_cooldown_minutes', 0) }}"
                                        min="0"
                                    >
                                    <span class="hec-help-text">How long after exiting before re-entry is allowed. 0 = no cooldown.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="hec-form-actions">
                    <a href="{{ route('ticket-packages.index') }}" class="hec-btn-secondary">Cancel</a>
                    <button type="submit" class="hec-btn-primary">Submit Event for Review</button>
                </div>
            </form>
        </div>
    </section>
</div>

<style>
    /* ── Page Layout ── */
    .hec-page {
        min-height: 100vh;
        background: linear-gradient(135deg, rgba(11, 18, 32, 0.95) 0%, rgba(20, 12, 20, 0.95) 100%);
        padding-top: 4rem;
    }

    .hec-hero {
        padding: 3rem 1.25rem 2rem;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        margin-bottom: 3rem;
    }

    .hec-hero-inner {
        max-width: 700px;
        margin: 0 auto;
    }

    .hec-heading {
        margin: 0 0 1rem;
        font-size: clamp(1.8rem, 4vw, 2.6rem);
        font-weight: 800;
        line-height: 1.2;
        color: #fff;
    }

    .hec-sub {
        margin: 0;
        font-size: 0.95rem;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.65);
    }

    /* ── Form Section ── */
    .hec-form-section {
        padding: 0 1.25rem 3rem;
    }

    .hec-shell {
        max-width: 900px;
        margin: 0 auto;
    }

    .hec-form {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 2rem;
    }

    /* ── Tabs ── */
    .hec-tabs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .hec-tab {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .hec-tab:hover {
        background: rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.8);
    }

    .hec-tab--active {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border-color: #2563eb;
        color: #fff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .hec-tab-icon {
        font-size: 1.1rem;
    }

    /* ── Tab Panels ── */
    .hec-tab-panel {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .hec-tab-panel--active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── Section ── */
    .hec-section {
        margin-bottom: 2rem;
    }

    .hec-section:last-child {
        margin-bottom: 0;
    }

    .hec-section-title {
        margin: 0 0 0.5rem;
        font-size: 1.1rem;
        font-weight: 700;
        color: #fff;
    }

    .hec-section-subtitle {
        margin: 0 0 1.5rem;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.6);
    }

    /* ── Form Groups ── */
    .hec-form-group {
        margin-bottom: 1.25rem;
    }

    .hec-form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .hec-label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.9);
    }

    .hec-required {
        color: #ef4444;
    }

    /* ── Inputs ── */
    .hec-input,
    .hec-select,
    .hec-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 6px;
        color: #fff;
        font-family: 'Quicksand', sans-serif;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .hec-input::placeholder,
    .hec-textarea::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .hec-input:focus,
    .hec-select:focus,
    .hec-textarea:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.09);
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .hec-input.is-invalid,
    .hec-select.is-invalid,
    .hec-textarea.is-invalid {
        border-color: #ef4444;
        background: rgba(239, 68, 68, 0.05);
    }

    .hec-textarea {
        resize: vertical;
        min-height: 120px;
    }

    .hec-select-wrapper {
        position: relative;
    }

    .hec-select {
        appearance: none;
        padding-right: 2.5rem;
        cursor: pointer;
    }

    .hec-select option {
        background: #1f2937;
        color: #fff;
        padding: 0.5rem 1rem;
    }

    .hec-select option:checked {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #fff;
    }

    .hec-select-wrapper::after {
        content: '▼';
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.7rem;
        color: rgba(255, 255, 255, 0.5);
        pointer-events: none;
    }

    /* ── Checkbox ── */
    .hec-checkbox {
        margin-right: 0.5rem;
        cursor: pointer;
    }

    .hec-checkbox-label {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.8);
        cursor: pointer;
    }

    /* ── Toggle ── */
    .hec-toggle-group {
        margin-bottom: 1.5rem;
    }

    .hec-toggle {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
    }

    .hec-toggle-input {
        appearance: none;
        width: 44px;
        height: 24px;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        cursor: pointer;
        position: relative;
        transition: background 0.3s ease;
    }

    .hec-toggle-input::before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background: #fff;
        border-radius: 50%;
        top: 1px;
        left: 1px;
        transition: left 0.3s ease;
    }

    .hec-toggle-input:checked {
        background: #2563eb;
        border-color: #2563eb;
    }

    .hec-toggle-input:checked::before {
        left: 21px;
    }

    .hec-toggle-label {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.85);
        user-select: none;
    }

    /* ── File Upload ── */
    .hec-file-upload {
        position: relative;
    }

    .hec-file-input {
        display: none;
    }

    .hec-file-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem 1.5rem;
        background: rgba(255, 255, 255, 0.04);
        border: 2px dashed rgba(255, 255, 255, 0.12);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .hec-file-label:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .hec-file-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
    }

    .hec-file-text {
        display: block;
        font-size: 0.95rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 0.25rem;
    }

    .hec-file-hint {
        display: block;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.5);
    }

    .hec-image-preview {
        position: relative;
        width: 100%;
        max-width: 300px;
        margin-top: 1rem;
        border-radius: 8px;
        overflow: hidden;
    }

    .hec-image-preview img {
        width: 100%;
        height: auto;
        display: block;
        border-radius: 8px;
    }

    .hec-image-remove {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        width: 32px;
        height: 32px;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        border: none;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease;
    }

    .hec-image-remove:hover {
        background: rgba(0, 0, 0, 0.8);
    }

    /* ── Repeater (Ticket Categories) ── */
    .hec-repeater {
        margin-bottom: 1.5rem;
    }

    .hec-repeater-item {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        position: relative;
    }

    .hec-repeater-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .hec-repeater-item-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.85);
    }

    .hec-repeater-item-remove {
        background: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, 0.3);
        padding: 0.4rem 0.8rem;
        border-radius: 4px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .hec-repeater-item-remove:hover {
        background: rgba(239, 68, 68, 0.3);
        color: #fff;
    }

    /* ── Buttons ── */
    .hec-btn-primary,
    .hec-btn-secondary {
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Quicksand', sans-serif;
    }

    .hec-btn-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .hec-btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        transform: translateY(-2px);
    }

    .hec-btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .hec-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        border-color: rgba(255, 255, 255, 0.3);
    }

    /* ── Form Actions ── */
    .hec-form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    /* ── Error Messages ── */
    .hec-error {
        display: block;
        margin-top: 0.375rem;
        font-size: 0.8rem;
        color: #fca5a5;
    }

    .hec-help-text {
        display: block;
        margin-top: 0.375rem;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.5);
    }

    /* ── Responsive ── */
    @media (max-width: 780px) {
        .hec-page { padding-top: 3rem; }
        .hec-hero { padding: 2rem 1rem 1.5rem; }
        .hec-form-section { padding: 0 1rem 2rem; }
        .hec-form { padding: 1.5rem; }
        .hec-tabs { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
        .hec-form-actions { flex-direction: column; }
        .hec-form-actions > * { width: 100%; }
    }

    @media (max-width: 580px) {
        .hec-heading { font-size: 1.5rem; }
        .hec-form-row { grid-template-columns: 1fr; }
        .hec-file-label { padding: 2rem 1rem; }
        .hec-tabs { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabs = document.querySelectorAll('.hec-tab');
    const panels = document.querySelectorAll('.hec-tab-panel');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Remove active state from all tabs and panels
            tabs.forEach(t => t.classList.remove('hec-tab--active'));
            panels.forEach(p => p.classList.remove('hec-tab-panel--active'));
            
            // Add active state to clicked tab and corresponding panel
            this.classList.add('hec-tab--active');
            document.querySelector(`[data-tab-panel="${tabName}"]`).classList.add('hec-tab-panel--active');
        });
    });

    // Create new category checkbox
    const createCategoryCheckbox = document.getElementById('create_category');
    const newCategoryInput = document.getElementById('new-category-input');
    const categorySelect = document.getElementById('category_id');

    createCategoryCheckbox.addEventListener('change', function() {
        if (this.checked) {
            newCategoryInput.style.display = 'block';
            categorySelect.disabled = true;
            categorySelect.value = '';
        } else {
            newCategoryInput.style.display = 'none';
            categorySelect.disabled = false;
        }
    });

    // Re-entry toggle
    const reentryToggle = document.getElementById('reentry_allowed');
    const reentryOptions = document.getElementById('reentry-options');

    reentryToggle.addEventListener('change', function() {
        reentryOptions.style.display = this.checked ? 'block' : 'none';
    });

    if (reentryToggle.checked) {
        reentryOptions.style.display = 'block';
    }

    // Free event toggle
    const isFreeToggle = document.getElementById('is_free');
    isFreeToggle.addEventListener('change', updateTicketCategoryPriceFields);

    // Image upload preview
    const imageInput = document.getElementById('image_url');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const imageRemoveBtn = document.querySelector('.hec-image-remove');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    imageRemoveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        imageInput.value = '';
        imagePreview.style.display = 'none';
    });

    // Ticket categories repeater
    const ticketCategoriesContainer = document.getElementById('ticket-categories-container');
    const addTicketCategoryBtn = document.getElementById('add-ticket-category');
    let ticketCategoryCount = 0;

    function createTicketCategoryRow(index, data = {}) {
        const isFree = isFreeToggle.checked;
        const row = document.createElement('div');
        row.className = 'hec-repeater-item';
        row.dataset.index = index;
        
        row.innerHTML = `
            <div class="hec-repeater-item-header">
                <span class="hec-repeater-item-title">Category ${index + 1}</span>
                <button type="button" class="hec-repeater-item-remove" onclick="this.closest('.hec-repeater-item').remove()">Remove</button>
            </div>
            <div class="hec-form-row">
                <div class="hec-form-group">
                    <label class="hec-label">Category Name <span class="hec-required">*</span></label>
                    <input 
                        type="text" 
                        name="ticket_categories[${index}][name]" 
                        class="hec-input"
                        value="${data.name || ''}"
                        placeholder="e.g. VIP"
                        required
                    >
                </div>
                <div class="hec-form-group">
                    <label class="hec-label">Price (UGX) ${isFree ? '(disabled for free events)' : '<span class="hec-required">*</span>'}</label>
                    <input 
                        type="number" 
                        name="ticket_categories[${index}][price]" 
                        class="hec-input ticket-price"
                        value="${data.price || ''}"
                        placeholder="50000"
                        min="0"
                        ${isFree ? 'disabled' : 'required'}
                    >
                </div>
                <div class="hec-form-group">
                    <label class="hec-label">Number of Tickets <span class="hec-required">*</span></label>
                    <input 
                        type="number" 
                        name="ticket_categories[${index}][ticket_count]" 
                        class="hec-input"
                        value="${data.ticket_count || ''}"
                        placeholder="100"
                        min="1"
                        required
                    >
                </div>
            </div>
            <div class="hec-form-group">
                <label class="hec-label">Description</label>
                <textarea 
                    name="ticket_categories[${index}][description]" 
                    class="hec-textarea"
                    placeholder="e.g. Front row seating, lounge access…"
                    rows="2"
                >${data.description || ''}</textarea>
            </div>
        `;
        
        ticketCategoriesContainer.appendChild(row);
    }

    function updateTicketCategoryPriceFields() {
        const isFree = isFreeToggle.checked;
        const priceInputs = document.querySelectorAll('.ticket-price');
        priceInputs.forEach(input => {
            input.disabled = isFree;
            input.required = !isFree;
        });
    }

    addTicketCategoryBtn.addEventListener('click', function(e) {
        e.preventDefault();
        createTicketCategoryRow(ticketCategoryCount);
        ticketCategoryCount++;
    });

    // Initialize with one empty ticket category
    createTicketCategoryRow(ticketCategoryCount);
    ticketCategoryCount++;

    // Form submission
    const form = document.querySelector('.hec-form');
    form.addEventListener('submit', async function(e) {
        const categorySelect = document.getElementById('category_id');
        const newCategoryInput = document.getElementById('new_category_name');
        const createCategoryCheckbox = document.getElementById('create_category');

        if (createCategoryCheckbox.checked && newCategoryInput.value.trim()) {
            // Create new category via API before submitting form
            e.preventDefault();
            
            const categoryName = newCategoryInput.value.trim();
            const submitBtn = form.querySelector('[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating category...';

            try {
                const response = await fetch('{{ route("api.categories.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({ name: categoryName }),
                });

                const raw = await response.text();
                let data;

                try {
                    data = JSON.parse(raw);
                } catch (parseError) {
                    throw new Error('Server returned an unexpected response. Please refresh and try again.');
                }

                if (!response.ok) {
                    const errorMsg = data.errors?.name?.[0] || data.message || 'Failed to create category';
                    alert('Error: ' + errorMsg);
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    return;
                }

                // Add the new category to the select
                const option = document.createElement('option');
                option.value = data.category.id;
                option.textContent = data.category.name;
                categorySelect.appendChild(option);
                
                // Select the new category
                categorySelect.value = data.category.id;

                if (data.existing === true) {
                    alert('Category already existed. We selected it for you and will continue submission.');
                }
                
                // Uncheck the create category checkbox and hide input
                createCategoryCheckbox.checked = false;
                newCategoryInput.style.display = 'none';
                categorySelect.disabled = false;
                newCategoryInput.value = '';

                submitBtn.disabled = false;
                submitBtn.textContent = originalText;

                // Now submit the form
                form.submit();
            } catch (error) {
                alert('Error creating category: ' + error.message);
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
            return;
        }

        if (!categorySelect.value) {
            e.preventDefault();
            alert('Please select or create a category');
            return;
        }
    });
});
</script>

@endsection
