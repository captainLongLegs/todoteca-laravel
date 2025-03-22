@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Collection</h1>
    <div class="row">
        @foreach (auth()->user()->books as $book)
        <div class="col-md-4 mb-4">
            <div class="card">
                @if ($book->cover_image)
                    <img src="{{ $book->cover_image }}" class="card-img-top" alt="{{ $book->title }}">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $book->title }}</h5>
                    <p class="card-text">{{ $book->author }}</p>
                    <p><strong>Status:</strong> {{ $book->pivot->status }}</p>
                    <p><strong>Rating:</strong> {{ $book->pivot->rating }}</p>
                    <p><strong>Comment:</strong> {{ $book->pivot->comment }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection