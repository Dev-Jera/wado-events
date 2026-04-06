@extends('layouts.admin')

@section('title', 'Categories')
@section('heading', 'Event Categories')

@section('content')
    @if (session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif

    <section class="categories-grid">
        <article class="category-panel">
            <h2>Create category</h2>
            <p>Add categories here, then select them when creating events.</p>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="category-form">
                @csrf
                <label class="field">
                    <span>Name</span>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                    @error('name') <small>{{ $message }}</small> @enderror
                </label>

                <label class="field">
                    <span>Description</span>
                    <textarea name="description" rows="5" placeholder="Short description for admins and future public use.">{{ old('description') }}</textarea>
                    @error('description') <small>{{ $message }}</small> @enderror
                </label>

                <button type="submit" class="primary-btn">Save category</button>
            </form>
        </article>

        <article class="category-panel">
            <h2>Available categories</h2>
            <div class="category-list">
                @forelse ($categories as $category)
                    <div class="category-item">
                        <div>
                            <strong>{{ $category->name }}</strong>
                            <p>{{ $category->description ?: 'No description yet.' }}</p>
                        </div>
                        <span>{{ $category->events_count }} events</span>
                    </div>
                @empty
                    <p>No categories yet. Create your first one on the left.</p>
                @endforelse
            </div>
        </article>
    </section>

    <style>
        .flash-success, .category-panel { background: #fff; border-radius: 24px; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.07); }
        .flash-success { padding: 1rem 1.2rem; color: #166534; margin-bottom: 1rem; border: 1px solid #bbf7d0; background: #f0fdf4; }
        .categories-grid { display: grid; grid-template-columns: minmax(320px, 0.9fr) minmax(320px, 1.1fr); gap: 1rem; }
        .category-panel { padding: 1.5rem; }
        .category-panel h2 { margin-top: 0; }
        .category-panel p { color: #667085; line-height: 1.6; }
        .category-form { display: grid; gap: 1rem; margin-top: 1rem; }
        .field { display: grid; gap: 0.45rem; color: #344054; font-weight: 600; }
        .field input, .field textarea { width: 100%; border: 1px solid #d0d5dd; border-radius: 16px; padding: 0.9rem 1rem; font: inherit; color: #101828; background: #fff; }
        .field small { color: #b42318; }
        .primary-btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.9rem 1.1rem; border-radius: 999px; border: 0; background: linear-gradient(135deg, #f15a24, #dc2626); color: #fff; text-decoration: none; font: inherit; font-weight: 700; cursor: pointer; }
        .category-list { display: grid; gap: 0.85rem; margin-top: 1rem; }
        .category-item { display: flex; justify-content: space-between; align-items: start; gap: 1rem; padding: 1rem; border-radius: 18px; background: #f8fafc; }
        .category-item p { margin: 0.3rem 0 0; }
        .category-item span { color: #b45309; font-weight: 700; white-space: nowrap; }
        @media (max-width: 960px) { .categories-grid { grid-template-columns: 1fr; } }
    </style>
@endsection
