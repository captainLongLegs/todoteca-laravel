@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Book Collection</h1>
    <p>This page displays the books you have added to your collection.</p>

    {{-- Display Session Messages --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Check if the $books collection passed from the controller exists and has items --}}
    @if (isset($books) && $books->count() > 0)
         <div class="row">
            @foreach ($books as $book)
                <div class="col-md-6 col-lg-4 mb-4"> 
                    <div class="card h-100"> 
                        {{-- Display Cover Image --}}
                        {{-- Check if cover_image exists in your books table --}}

                        {{-- Also check if Book model uses 'cover_image' or maybe 'background_image' like videogames? Adapt property name. --}}
                        @if ($book->cover_image)
                            {{-- Style image to prevent distortion --}}
                            <img src="{{ $book->cover_image }}" class="card-img-top" alt="{{ $book->title }} Cover" style="height: 250px; object-fit: cover;">
                        @else
                             <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 250px;">
                                <span>No Cover Image</span>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column"> {{-- Flex column for button spacing --}}
                            <h5 class="card-title">{{ $book->title }}</h5>
                            <p class="card-text text-muted"><em>by {{ $book->author ?? 'Unknown Author' }}</em></p>

                            {{-- Display Pivot Data using Badges --}}
                            <div class="mb-2">
                                <span class="badge bg-primary">Status: {{ Str::of($book->pivot->status ?? 'N/A')->replace('-', ' ')->title() }}</span>
                                @if($book->pivot->rating)
                                    <span class="badge bg-warning text-dark">Rating: {{ $book->pivot->rating }}/5</span>
                                @endif
                                {{-- Add other pivot data badges if applicable --}}
                            </div>

                            @if($book->pivot->comment)
                                <p class="card-text fst-italic border-start border-3 border-light ps-2">
                                    <small>Comment: {{ $book->pivot->comment }}</small>
                                </p>
                            @endif

                            <small class="text-muted mt-auto">Added: {{ $book->pivot->created_at ? $book->pivot->created_at->format('Y-m-d') : 'N/A' }}</small>
                                {{-- mt-auto pushes timestamp to bottom --}}
                        </div>

                        {{-- Card Footer for Actions --}}
                        <div class="card-footer bg-light">
                             <div class="btn-group w-100"> {{-- Use btn-group for consistent button look --}}
                                {{-- Edit Button/Link --}}
                                <a href="{{ route('my-books.edit', $book->id) }}" class="btn btn-secondary btn-sm">Edit</a>

                                {{-- Remove Button Form --}}
                                <form action="{{ route('my-books.destroy', $book->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove \'{{ addslashes($book->title) }}\' from your collection?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Add Pagination if using paginate in controller --}}
        {{-- <div class="mt-3">
             {{ $books->links() }}
        </div> --}}

    @else
         {{-- Message if collection is empty --}}
         <p>You haven't added any books to your collection yet.</p>
         <a href="{{ route('books.search') }}" class="btn btn-primary">Search for books to add!</a>
    @endif
</div>
@endsection