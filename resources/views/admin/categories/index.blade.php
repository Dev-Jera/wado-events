@extends('layouts.admin')

@section('title', 'Categories')
@section('heading', 'Event Categories')

@section('content')
    @if (session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif
    
    @if (session('error'))
        <div class="flash-error">{{ session('error') }}</div>
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
                    <div class="category-item" id="category-{{ $category->id }}">
                        <div>
                            <strong>{{ $category->name }}</strong>
                            <p>{{ $category->description ?: 'No description yet.' }}</p>
                        </div>
                        <div class="category-actions">
                            <span>{{ $category->events_count }} events</span>
                            @if($category->events_count == 0)
                                <button class="delete-icon-btn" onclick="deleteCategory({{ $category->id }})" title="Delete category">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                        <path d="M8 4V3c0-1 1-2 2-2h4c1 0 2 1 2 2v1"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            @else
                                <button class="disabled-icon-btn" disabled title="Cannot delete: Category has {{ $category->events_count }} event(s)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                        <path d="M8 4V3c0-1 1-2 2-2h4c1 0 2 1 2 2v1"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p>No categories yet. Create your first one on the left.</p>
                @endforelse
            </div>
        </article>
    </section>

    <style>
        .flash-success, .flash-error, .category-panel { background: #fff; border-radius: 24px; box-shadow: 0 18px 40px rgba(15, 23, 42, 0.07); }
        .flash-success { padding: 1rem 1.2rem; color: #166534; margin-bottom: 1rem; border: 1px solid #bbf7d0; background: #f0fdf4; }
        .flash-error { padding: 1rem 1.2rem; color: #991b1b; margin-bottom: 1rem; border: 1px solid #fecaca; background: #fef2f2; }
        .categories-grid { display: grid; grid-template-columns: minmax(320px, 0.9fr) minmax(320px, 1.1fr); gap: 1rem; }
        .category-panel { padding: 1.5rem; }
        .category-panel h2 { margin-top: 0; }
        .category-panel p { color: #000000; line-height: 1.6; }
        .category-form { display: grid; gap: 1rem; margin-top: 1rem; }
        .field { display: grid; gap: 0.45rem; color: #344054; font-weight: 600; }
        .field input, .field textarea { width: 100%; border: 1px solid #d0d5dd; border-radius: 16px; padding: 0.9rem 1rem; font: inherit; color: #101828; background: #fff; }
        .field small { color: #b42318; }
        .primary-btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.9rem 1.1rem; border-radius: 999px; border: 0; background: linear-gradient(135deg, #f15a24, #dc2626); color: #fff; text-decoration: none; font: inherit; font-weight: 700; cursor: pointer; transition: transform 0.2s, opacity 0.2s; }
        .primary-btn:hover { opacity: 0.9; transform: scale(1.02); }
        .category-list { display: grid; gap: 0.85rem; margin-top: 1rem; }
        .category-item { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1rem; border-radius: 18px; background: #f8fafc; transition: all 0.2s; }
        .category-item p { margin: 0.3rem 0 0; }
        .category-item span { color: #b45309; font-weight: 700; white-space: nowrap; }
        .category-actions { display: flex; align-items: center; gap: 0.75rem; }
        .delete-icon-btn, .disabled-icon-btn { background: none; border: none; cursor: pointer; padding: 0.5rem; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .delete-icon-btn { color: #dc2626; }
        .delete-icon-btn:hover { background: #fee2e2; transform: scale(1.1); }
        .disabled-icon-btn { color: #9ca3af; cursor: not-allowed; opacity: 0.5; }
        @media (max-width: 960px) { .categories-grid { grid-template-columns: 1fr; } }
    </style>

    <script>
        function deleteCategory(categoryId) {
            if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) return;

            fetch(`/admin/categories/${categoryId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                if (status === 200) {
                    // Remove the category element from the DOM
                    const categoryElement = document.getElementById(`category-${categoryId}`);
                    if (categoryElement) {
                        categoryElement.remove();
                    }
                    
                    // Show success message
                    showFlashMessage(body.message || 'Category deleted successfully!', 'success');
                    
                    // Reload page after 1 second to update events counts
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showFlashMessage(body.message || body.error || 'Failed to delete the category.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showFlashMessage('An error occurred. Please try again.', 'error');
            });
        }
        
        function showFlashMessage(message, type) {
            // Remove existing flash messages
            const existingSuccess = document.querySelector('.flash-success');
            const existingError = document.querySelector('.flash-error');
            if (existingSuccess) existingSuccess.remove();
            if (existingError) existingError.remove();
            
            // Create new flash message
            const flashDiv = document.createElement('div');
            flashDiv.className = `flash-${type}`;
            flashDiv.textContent = message;
            
            // Insert at the top of the content area
            const contentSection = document.querySelector('.categories-grid');
            if (contentSection) {
                contentSection.parentNode.insertBefore(flashDiv, contentSection);
            }
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                flashDiv.style.opacity = '0';
                setTimeout(() => flashDiv.remove(), 300);
            }, 3000);
        }
    </script>
@endsection