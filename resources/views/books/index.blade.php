@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">

            <h1>Books</h1>
            <a href="{{ route('books.create') }}" class="btn btn-primary mb-3">Add New Book</a>
        </div>


        {{-- Session Messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Sorting links --}}
        <div class="d-flex justify-content-end gap-2 mb-3 small">
            <span>Sort by:</span>
            {{-- Helper function to generate sort links --}}
            @php
                // Function to generate sort link, preserving other query parameters
                // Toggles direction if current column is selected
                function sort_link($column, $label, $currentSortBy, $currentSortDirection)
                {
                    $newSortDirection = ($currentSortBy == $column && $currentSortDirection == 'asc') ? 'desc' : 'asc';
                    // Generates URL keeping existing query parameters but overriding sort_by and sort_dir
                    $url = request()->fullUrlWithQuery(['sort_by' => $column, 'sort_dir' => $newSortDirection]);
                    $arrow = '';

                    if ($currentSortBy == $column) {
                        $arrow = $currentSortDirection == 'asc' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>';
                    }

                    // Adds 'fw-bold' if this column is currently active
                    $activeClass = $currentSortBy == $column ? 'fw-bold' : '';

                    return '<a href="' . e($url) . '" class="' . $activeClass . '">' . e($label) . ' ' . $arrow . '</a>';
                }        
            @endphp
            {!! sort_link('title', 'Title', $sortBy, $sortDir) !!} |
            {!! sort_link('author', 'Author', $sortBy, $sortDir) !!} |
            {!! sort_link('created_at', 'Date Added', $sortBy, $sortDir) !!}
        </div>

        {{-- Check for empty results --}}
        @php
            $isEmptyCheck = ($books instanceof \Illuminate\Pagination\LengthAwarePaginator || $books instanceof \Illuminate\Support\Collection)
                ? $books->isEmpty()
                : empty($books);
        @endphp

        @if ($isEmptyCheck)
            <div class="alert alert-info">No books found in the local database</div>
        @else
            <div class="row">
                @foreach ($books as $book)
                    <div class="col-md-4-6 col-lg-4 mb-a">
                        <div class="card h-100">
                            @if ($book->cover_image)
                                <img src="{{ $book->cover_image }} " class="card-img-top" alt="{{ $book->title }} Cover"
                                    style="height: 250px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center"
                                    style="height: 200px;">
                                    <span>No Cover Image</span>
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $book->title }}</h5>
                                <p class="card-text text-muted"><em>by {{ $book->author ?? 'Unknown Author'}}</em></p>

                                <!--Add to My Collection  -->
                                @auth
                                    <form action="{{ route('books.add-local-to-collection', $book->id) }}" method="POST">
                                        @csrf
                                        {{-- Status Select --}}
                                        <div class="form-group mb-2">
                                            <label for="status_{{ $book->id }}" class="form-label fw-bold small">Status:</label>
                                            <select name="status" id="status_{{ $book->id }}" class="form-select form-select-sm"
                                                required>
                                                @php $statuses = ['to-read', 'reading', 'read', 'on-hold', 'dnf']; @endphp
                                                @foreach($statuses as $statusOption)
                                                    <option value="{{ $statusOption }}" {{ $statusOption == 'to-read' ? 'selected' : '' }}>
                                                        {{ Str::of($statusOption)->replace('-', ' ')->title() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- Rating Input --}}
                                        <div class="form-group mb-2">
                                            <label for="rating_{{ $book->id }}" class="form-label fw-bold small">Rating (1-5):</label>

                                            <input type="number" name="rating" id="rating_{{ $book->id }}"
                                                class="form-control form-control-sm" min="1" max="5">
                                        </div>
                                        {{-- Comment Textarea --}}
                                        <div class="form-group mb-2">
                                            <label for="comment_{{ $book->id }}" class="form-label fw-bold small">Comment:</label>
                                            <textarea name="comment" id="comment_{{ $book->id }}" class="form-control form-control-sm"
                                                rows="2"></textarea>
                                        </div>
                                        {{-- Submit Button --}}
                                        <button type="submit" class="btn btn-success btn-sm w-100 mt-2">Add to My Collection</button>

                                    </form>
                                @else
                                    <p class="text-muted mt-auto pt-3 border-top"><a href="{{ route('login') }}">Log in</a> to add this
                                        book to your collection.</p>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                @if ($books instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $books->links() }}
                @endif
            </div>
        @endif
    </div>
@endsection