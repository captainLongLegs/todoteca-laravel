@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Search Videogames</h1>
    <p>This page will contain the form to search for videogames via the API.</p>
    {{-- Placeholder for search form --}}
    <form action="{{ route('videogames.search.results') }}" method="GET">
         {{-- We'll add input fields here later --}}
         <div class="mb-3">
             <label for="query" class="form-label">Search Term:</label>
             <input type="text" name="query" id="query" class="form-control" placeholder="Enter game title, platform, etc.">
         </div>
         <button type="submit" class="btn btn-primary">Search</button>
    </form>
</div>
@endsection