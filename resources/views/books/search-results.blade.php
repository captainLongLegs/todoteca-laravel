@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Search Results</h1>
    @if (count($books) > 0)
        <ul>
            @foreach ($books as $book)
                <li>
                    <strong>{{ $book['title'] }}</strong>
                    <p>Author(s): {{ implode(', ', $book['author_name'] ?? ['Unknown']) }}</p>
                    <p>ISBN: {{ $book['isbn'][0] ?? 'Not available' }}</p>
                    <p>First Published: {{ $book['first_publish_year'] ?? 'Unknown' }}</p>
                    <form action="{{ route('user-books.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="title" value="{{ $book['title'] }}">
                        <input type="hidden" name="author" value="{{ implode(',', $book['author_name'] ?? ['Unknown']) }}">
                        <input type="hidden" name="isbn" value="{{ $book['isbn'][0] ?? '' }}">

                        <!-- Metadata fields -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="to-read">To Read</option>
                                <option value="reading">Reading</option>
                                <option value="read">Read</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating (1-5)</label>
                            <input type="number" name="rating" id="rating" class="form-control" min="1" max="5" step="0.1">
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Comment</label>
                            <textarea name="comment" id="comment" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Add to My Collection</button>  
                    </form>
                </li>
            @endforeach
        </ul>
        @else
            <p>No books found. Please try again.</p>
    @endif
</div>
@endsection
