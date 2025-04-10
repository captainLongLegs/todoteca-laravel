@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Videogame Search Results</h1>
    <p>This page will display the videogame search results from the API for the query: "{{ $query ?? 'N/A' }}"</p>

    @isset($error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endisset

    @isset($videogames)
        @if (count($videogames) > 0)
            <p>Found {{ count($videogames) }} results.</p>
            {{-- Placeholder for results list --}}
            {{-- We will loop through $videogames here later --}}
        @else
            <p>No videogames found matching your query.</p>
        @endif
    @else
        <p>Perform a search to see results.</p> {{-- Shown if $videogames isn't passed --}}
    @endisset

</div>
@endsection