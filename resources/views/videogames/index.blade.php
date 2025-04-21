@extends('layouts.app')

@section('content')
<div class="container">
    <h1>All Videogames (Local DB)</h1>
    <p>This page lists all videogames currently saved in the local database.</p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (isset($videogames) && $videogames->count() > 0)
        <div class="list-group">
            @foreach ($videogames as $game)
                <div class="list-group-item">
                    {{-- Remember we renamed 'title' to 'name' --}}
                    <strong>{{ $game->name }}</strong>
                    <small> (Developer: {{ $game->developer ?? 'N/A' }})</small>
                    {{-- Add link to a 'show' page later if needed --}}
                    {{-- <a href="{{ route('videogames.show', $game->id) }}">View Details</a> --}}
                </div>
            @endforeach
        </div>
         {{-- Pagination links --}}
         <div class="mt-3">
             {{ $videogames->links() }}
         </div>
    @else
        <p>No videogames found in the local database yet.</p>
    @endif
</div>
@endsection