@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Books</h1>
    <a href="{{ route('books.create') }}" class="btn btn-primary mb-3">Add New Book</a>
    @if ($books->isEmpty())
    <div class="alert alert-info">No books found</div>
    @else
    <div class="row">
        @foreach ($books as $book)
        <div class="col-md-4 mb-a">
            <div class="card">
                @if ($book->cover_image)
                <img src="{{ $book->cover_image }} " class="card-img-top" alt="{{ $book->title }}">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $book->title }}</h5>
                    <p class="card-text">{{ $book->author }}</p>

                <!--DEBUGGING-->
                <p>Book ID: {{  $book->id }}</p>

                <!--Add to My Collection  -->
                <form action="{{  route('user-books.store', $book) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for=status >Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="to-read">To Read</option>
                            <option value="reading">Reading</option>
                            <option value="read">Read</option>
                        </select>                    
                    </div>
                    <div class="form-group">
                        <label for="rating">Rating (1-5)</label>
                        <input type="number" name="rating" id="rating" class="form-control" min="1" max="5">
                    </div>
                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea name="comment" id="comment" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success mt-2">Add to My Collection</button>
                </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection