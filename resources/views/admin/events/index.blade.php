@extends('layouts.admin')

@section('title', 'Edit Event')
@section('heading', 'Edit Event')

@section('content')

<div class="event-form-container">
    {{-- Header --}}
    <div class="form-header">
        <div class="header-left">
            <a href="{{ route('admin.events.index') }}" class="back-link">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Back to Events
            </a>
            <h1>Edit Event</h1>
            <p>Update event details, tickets, and media</p>
        </div>
        <div class="header-actions">
            <span class="status-badge status-{{ $event->status }}">
                {{ ucfirst($event->status) }}
            </span>
        </div>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data" class="event-form">
        @csrf
        @method('PUT')

        <div class="form-grid">
            {{-- Main Column --}}
            <div class="form-main">
                {{-- Basic Information Section --}}
                <div class="form-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title">Basic Information</h3>
                            <p class="card-subtitle">Core event details and description</p>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="field-group">
                            <label class="field-label required">Event Title</label>
                            <input type="text" name="title" class="field-input" value="{{ old('title', $event->title) }}" placeholder="e.g., Annual Tech Conference 2026">
                            @error('title') <span class="field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="field-row">
                            <div class="field-group">
                                <label class="field-label required">Category</label>
                                <select name="category_id" class="field-select">
                                    <option value="">Select category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field-group">
                                <label class="field-label required">Status</label>
                                <select name="status" class="field-select">
                                    <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $event->status) == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="field-label required">Description</label>
                            <textarea name="description" class="field-textarea" rows="5" placeholder="Describe your event...">{{ old('description', $event->description) }}</textarea>
                            @error('description') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Location Section --}}
                <div class="form-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title">Location</h3>
                            <p class="card-subtitle">Where will this event take place?</p>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="field-group">
                            <label class="field-label required">Venue Name</label>
                            <input type="text" name="venue" class="field-input" value="{{ old('venue', $event->venue) }}" placeholder="e.g., Kampala Serena Hotel">
                        </div>

                        <div class="field-row">
                            <div class="field-group">
                                <label class="field-label">City</label>
                                <input type="text" name="city" class="field-input" value="{{ old('city', $event->city) }}" placeholder="e.g., Kampala">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Country</label>
                                <input type="text" name="country" class="field-input" value="{{ old('country', $event->country) }}" placeholder="e.g., Uganda">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Date & Time Section --}}
                <div class="form-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title">Schedule</h3>
                            <p class="card-subtitle">When does your event start and end?</p>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="field-row">
                            <div class="field-group">
                                <label class="field-label required">Starts At</label>
                                <input type="datetime-local" name="starts_at" class="field-input" value="{{ old('starts_at', $event->starts_at ? $event->starts_at->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div class="field-group">
                                <label class="field-label required">Ends At</label>
                                <input type="datetime-local" name="ends_at" class="field-input" value="{{ old('ends_at', $event->ends_at ? $event->ends_at->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Media Section --}}
                <div class="form-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="2" width="20" height="20" rx="2.18"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title">Event Image</h3>
                            <p class="card-subtitle">Upload a cover image for your event</p>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        @if($event->image)
                            <div class="current-image">
                                <img src="{{ Storage::url($event->image) }}" alt="Current event image">
                                <button type="button" class="remove-image" data-image="{{ $event->image }}">Remove</button>
                            </div>
                        @endif
                        
                        <div class="upload-area" x-data="{ dragging: false }" @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop.prevent="dragging = false">
                            <input type="file" name="image" id="image" class="upload-input" accept="image/*">
                            <label for="image" class="upload-label" :class="{ 'dragging': dragging }">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="2" y="2" width="20" height="20" rx="2.18"/>
                                    <circle cx="8.5" cy="8.5" r="1.5"/>
                                    <polyline points="21 15 16 10 5 21"/>
                                </svg>
                                <span class="upload-title">Drag & drop your files or <span>Browse</span></span>
                                <span class="upload-hint">Upload an image from your computer (PNG, JPG up to 5MB)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column --}}
            <div class="form-sidebar">
                {{-- Ticket Pricing Card --}}
                <div class="sidebar-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 12V8H4v4M20 12v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4"/>
                                <line x1="12" y1="2" x2="12" y2="6"/>
                                <circle cx="12" cy="14" r="2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title">Ticket Pricing</h3>
                            <p class="card-subtitle">Set the base ticket price</p>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="field-group">
                            <label class="field-label required">Ticket Price (UGX)</label>
                            <div class="price-input-wrapper">
                                <span class="currency">UGX</span>
                                <input type="number" name="ticket_price" class="price-input" value="{{ old('ticket_price', $event->ticket_price) }}" step="1000" min="0">
                            </div>
                        </div>
                        
                        <div class="field-group">
                            <label class="field-label required">Total Capacity</label>
                            <input type="number" name="capacity" class="field-input" value="{{ old('capacity', $event->capacity) }}" placeholder="Maximum attendees">
                        </div>
                        
                        <div class="field-group">
                            <label class="field-label">Tickets Available</label>
                            <input type="number" name="tickets_available" class="field-input" value="{{ old('tickets_available', $event->tickets_available) }}" placeholder="Available for sale">
                        </div>
                    </div>
                </div>

                {{-- Organizer Card --}}
                <div class="sidebar-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="card-title">Organizer</h3>
                            <p class="card-subtitle">Who's hosting this event?</p>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="field-group">
                            <label class="field-label">Organizer Name</label>
                            <input type="text" name="organizer_name" class="field-input" value="{{ old('organizer_name', $event->organizer_name) }}" placeholder="e.g., Events Uganda">
                        </div>
                        
                        <div class="field-group">
                            <label class="field-label">Contact Email</label>
                            <input type="email" name="contact_email" class="field-input" value="{{ old('contact_email', $event->contact_email) }}" placeholder="organizer@example.com">
                        </div>
                        
                        <div class="field-group">
                            <label class="field-label">Contact Phone</label>
                            <input type="tel" name="contact_phone" class="field-input" value="{{ old('contact_phone', $event->contact_phone) }}" placeholder="+256 XXX XXX XXX">
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="sidebar-actions">
                    <button type="submit" class="btn-primary btn-block">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Save Changes
                    </button>
                    
                    <a href="{{ route('admin.events.index') }}" class="btn-secondary btn-block">
                        Cancel
                    </a>
                    
                    <button type="button" class="btn-danger btn-block" onclick="confirmDelete()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0h10"/>
                        </svg>
                        Delete Event
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Delete Form --}}
<form id="delete-form" method="POST" action="{{ route('admin.events.destroy', $event) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDelete() {
        if (confirm('Are you sure you want to delete "{{ $event->title }}"? This action cannot be undone.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>

<style>
/* --------------------------------------------
   EVENT FORM - Red, Navy Blue & White Theme
-------------------------------------------- */
.event-form-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0.5rem 0;
}

/* Form Header */
.form-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f2f5;
}

.header-left .back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    color: #64748b;
    text-decoration: none;
    font-size: 0.875rem;
    margin-bottom: 0.75rem;
    transition: color 0.15s;
}

.header-left .back-link:hover {
    color: #c8102e;
}

.header-left h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e2a5e;
    margin: 0 0 0.25rem 0;
}

.header-left p {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0;
}

.status-badge {
    display: inline-flex;
    padding: 0.4rem 1rem;
    border-radius: 30px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-draft {
    background: #fef3c7;
    color: #d97706;
}

.status-published {
    background: #ffebef;
    color: #c8102e;
}

.status-cancelled {
    background: #f1f5f9;
    color: #64748b;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 1.5rem;
}

/* Cards */
.form-card, .sidebar-card {
    background: white;
    border: 1px solid #eef2f6;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    background: #fafcfc;
    border-bottom: 1px solid #eef2f6;
}

.card-icon {
    width: 36px;
    height: 36px;
    background: #ffebef;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c8102e;
}

.card-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1e2a5e;
    margin: 0 0 0.2rem 0;
}

.card-subtitle {
    font-size: 0.7rem;
    color: #94a3b8;
    margin: 0;
}

.card-body {
    padding: 1.25rem;
}

/* Form Fields */
.field-group {
    margin-bottom: 1rem;
}

.field-group:last-child {
    margin-bottom: 0;
}

.field-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: #1e2a5e;
    margin-bottom: 0.4rem;
}

.field-label.required::after {
    content: '*';
    color: #c8102e;
    margin-left: 0.25rem;
}

.field-input, .field-select, .field-textarea {
    width: 100%;
    padding: 0.6rem 0.875rem;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.875rem;
    color: #1e293b;
    background: white;
    transition: all 0.2s;
}

.field-input:focus, .field-select:focus, .field-textarea:focus {
    outline: none;
    border-color: #c8102e;
    box-shadow: 0 0 0 3px rgba(200,16,46,0.08);
}

.field-textarea {
    resize: vertical;
    font-family: inherit;
}

.field-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.field-row .field-group {
    margin-bottom: 0;
}

.field-error {
    display: block;
    font-size: 0.7rem;
    color: #c8102e;
    margin-top: 0.25rem;
}

/* Price Input */
.price-input-wrapper {
    display: flex;
    align-items: center;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    background: white;
}

.price-input-wrapper .currency {
    padding: 0.6rem 0.75rem;
    background: #f8fafc;
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e2a5e;
    border-right: 1px solid #e2e8f0;
}

.price-input {
    flex: 1;
    padding: 0.6rem 0.875rem;
    border: none;
    font-size: 0.875rem;
    color: #1e293b;
}

.price-input:focus {
    outline: none;
}

/* Upload Area */
.current-image {
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.current-image img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.remove-image {
    padding: 0.3rem 0.75rem;
    background: #ffebef;
    border: none;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
    color: #c8102e;
    cursor: pointer;
}

.upload-area {
    position: relative;
}

.upload-input {
    position: absolute;
    opacity: 0;
    width: 0.1px;
    height: 0.1px;
}

.upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 2rem 1rem;
    border: 2px dashed #e2e8f0;
    border-radius: 12px;
    background: #fafcfc;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
}

.upload-label.dragging {
    border-color: #c8102e;
    background: #ffebef;
}

.upload-label svg {
    color: #94a3b8;
}

.upload-title {
    font-size: 0.875rem;
    font-weight: 500;
    color: #1e2a5e;
}

.upload-title span {
    color: #c8102e;
    text-decoration: underline;
}

.upload-hint {
    font-size: 0.7rem;
    color: #94a3b8;
}

/* Sidebar Actions */
.sidebar-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.btn-block {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.7rem 1rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all 0.15s;
}

.btn-primary {
    background: #c8102e;
    color: white;
}

.btn-primary:hover {
    background: #a00d27;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #f1f5f9;
    color: #475569;
}

.btn-secondary:hover {
    background: #e2e8f0;
}

.btn-danger {
    background: #ffebef;
    color: #c8102e;
}

.btn-danger:hover {
    background: #fed7df;
}

/* Responsive */
@media (max-width: 900px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-sidebar {
        order: 2;
    }
    
    .form-main {
        order: 1;
    }
}

@media (max-width: 640px) {
    .form-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .field-row {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .card-header {
        flex-wrap: wrap;
    }
}
</style>

@endsection