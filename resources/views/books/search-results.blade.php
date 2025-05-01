@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Book Search Results</h1>

    @if(isset($query) && $query)
        <p class="lead">Showing results for: <strong>"{{ $query }}"</strong></p>
    @endif

    {{-- Display Error Message --}}
    @isset($error)
         <div class="alert alert-danger">{{ $error }}</div>
    @endisset

    {{-- Check if books exist and there's no error --}}
    @if (isset($books) && count($books) > 0 && !isset($error))
        <div class="list-group">
            @foreach ($books as $book)
                 {{-- Use list-group-item for consistency with other views --}}
                <div class="list-group-item list-group-item-action mb-3">
                    <div class="row g-3">
                        {{-- Book Cover Image Column --}}
                        <div class="col-md-2 text-center">
                            {{-- Check if 'cover_i' exists and is not empty/negative --}}
                            @if (!empty($book['cover_i']) && $book['cover_i'] > 0)
                                <img src="https://covers.openlibrary.org/b/id/{{ $book['cover_i'] }}-M.jpg" alt="{{ $book['title'] }} Cover" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                            @else
                                {{-- Placeholder --}}
                                <div class="bg-secondary rounded text-white d-flex align-items-center justify-content-center" style="height: 150px; width: 100%;">
                                    <span>No Cover</span>
                                </div>
                            @endif
                        </div>

                        {{-- Book Details Column --}}
                        <div class="col-md-6">
                            <h5 class="mb-1">{{ $book['title'] ?? 'No Title' }}</h5>
                            <p class="mb-1"><small>Author(s): {{ implode(', ', $book['author_name'] ?? ['Unknown']) }}</small></p>
                            <p class="mb-1"><small>ISBN: {{ $book['isbn'][0] ?? 'Not available' }}</small></p>
                            <p class="mb-1"><small>First Published: {{ $book['first_publish_year'] ?? 'Unknown' }}</small></p>
                        </div>

                        {{-- Add to Collection Form Column --}}
                        <div class="col-md-4">
                            @auth
                            <form action="{{ route('books.store-from-search') }}" method="POST" class="mt-2">
                                @csrf
                                {{-- Hidden fields --}}
                                <input type="hidden" name="title" value="{{ $book['title'] ?? '' }}">
                                <input type="hidden" name="author" value="{{ implode(',', $book['author_name'] ?? ['Unknown']) }}">
                                {{-- ** IMPORTANT: Ensure BookController@storeFromSearch uses first ISBN ** --}}
                                <input type="hidden" name="isbn" value="{{ $book['isbn'][0] ?? '' }}">
                                {{-- ** ADD HIDDEN FIELD FOR COVER IMAGE to save locally ** --}}
                                @if (!empty($book['cover_i']) && $book['cover_i'] > 0)
                                     <input type="hidden" name="cover_image" value="https://covers.openlibrary.org/b/id/{{ $book['cover_i'] }}-M.jpg">
                                @endif

                                {{-- Status, Rating, Comment fields (as before) --}}
                                 <div class="mb-2">
                                    <label for="status_{{ $loop->index }}" class="form-label fw-bold small">Status:</label>
                                    <select name="status" id="status_{{ $loop->index }}" class="form-select form-select-sm" required>
                                        @php $statuses = ['to-read', 'reading', 'read', 'on-hold', 'abandoned']; @endphp
                                        @foreach($statuses as $statusOption)
                                            <option value="{{ $statusOption }}" {{ $statusOption == 'to-read' ? 'selected' : '' }}>
                                                {{ Str::of($statusOption)->replace('-', ' ')->title() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label for="rating_{{ $loop->index }}" class="form-label fw-bold small">Rating (1-5):</label>
                                    <input type="number" name="rating" id="rating_{{ $loop->index }}" class="form-control form-control-sm" min="1" max="5" step="1">
                                </div>
                                <div class="mb-2">
                                    <label for="comment_{{ $loop->index }}" class="form-label fw-bold small">Comment:</label>
                                    <textarea name="comment" id="comment_{{ $loop->index }}" class="form-control form-control-sm" rows="2"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100">Add to My Collection</button>
                            </form>
                            @else
                                 <p class="text-muted mt-3"><a href="{{ route('login') }}">Log in</a> to add books.</p>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif (!isset($error))
        <p>No books found matching your query "{{ $query ?? '' }}".</p>
    @endif
</div>
@endsection