@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">

            <h1>Books</h1>
            <a href="{{ route('books.create') }}" class="btn btn-primary mb-3">Add New Book</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

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
                                <img src="{{ $book->cover_image }} " class="card-img-top" alt="{{ $book->title }}">
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