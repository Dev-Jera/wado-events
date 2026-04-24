@extends('layouts.admin')

@section('title', 'Content Management')
@section('heading', 'Content Management')

@section('content')
<div class="content-management">
    <h2>Manage Home Page Content</h2>
    <p>Use this section to create and manage the content displayed on the home page.</p>

    <form action="{{ route('content-management.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="hero_title">Hero Section Title</label>
            <input type="text" id="hero_title" name="hero_title" class="form-control" placeholder="Enter hero section title">
        </div>

        <div class="form-group">
            <label for="hero_subtitle">Hero Section Subtitle</label>
            <input type="text" id="hero_subtitle" name="hero_subtitle" class="form-control" placeholder="Enter hero section subtitle">
        </div>

        <div class="form-group">
            <label for="hero_images">Hero Section Images</label>
            <input type="file" id="hero_images" name="hero_images[]" class="form-control" multiple>
        </div>

        <div class="form-group">
            <label for="ticket_packages">Ticket Packages</label>
            <textarea id="ticket_packages" name="ticket_packages" class="form-control" rows="5" placeholder="Enter ticket package details"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Content</button>
    </form>
</div>

<style>
    .content-management {
        background: #ffffff;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        font-family: var(--wado-app-font);
    }

    .content-management h2 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1rem;
    }

    .btn-primary {
        background-color: #1d4ed8;
        color: #ffffff;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }
</style>
@endsection