@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Search for Books</h1>
    <form action="{{ route('books.search.results') }}" method="GET">
        <div class="form-group">
            <label for="query">Search by Title, Author, or ISBN</label>
            <input type="text" name="query" id="query" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
</div>
@endsection
