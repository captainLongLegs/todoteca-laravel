@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">

            <h1>Local Books DB</h1>
            <a href="{{ route('books.create') }}" class="btn btn-primary">Add New Book</a>
        </div>


        {{-- Session Messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Search Bar & Sorting links --}}
        <div class="card mb-4">
            <div class="card-header">Search & Sort Books by: </div>
            <div class="card-body">
                <form action="{{ route('books.index') }}" method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by title, author, ISBN..."
                            value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if(request('search'))
                            <a href="{{ route('books.index', array_filter(request()->except('search', 'page'))) }}"
                                class="btn btn-secondary">Clear Search</a>
                        @endif
                    </div>
                </form>

                <div class="d-flex jusity-content-start flex-wrap gap-2 small">
                    <span class="me-2 align-self-center">Sort by:</span>

                    @php
                        // Function to generate sort link
                        function sort_link($column, $label, $currentSortBy, $currentSortDirection)
                        {
                            $searchParam = request()->has('search') ? ['search' => request('search')] : [];

                            $newSortDirection = ($currentSortBy == $column && $currentSortDirection == 'asc') ? 'desc' : 'asc';

                            $url = request()->fullurlWithQuery(array_merge($searchParam, ['sort_by' => $column, 'sort_dir' => $newSortDirection]));

                            $arrow = '';

                            if ($currentSortBy == $column) {
                                $arrow = $currentSortDirection == 'asc' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>';
                            }

                            $activeClass = $currentSortBy == $column ? 'fw-bold' : '';
                            return '<a href="' . e($url) . '" class="btn btn-outline-secondary btn-sm ' . $activeClass . '">' . e($label) . ' ' . $arrow . '</a>';
                        }
                    @endphp
                    {!! sort_link('title', 'Title', $sortBy, $sortDir) !!} |
                    {!! sort_link('author', 'Author', $sortBy, $sortDir) !!} |
                    {!! sort_link('created_at', 'Date Added', $sortBy, $sortDir) !!}
                </div>
            </div>
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