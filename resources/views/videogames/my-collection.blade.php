@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>My Videogame Collection</h1>
        <p>This page displays the videogames you have added to your collection.</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif


        @if (isset($videogames) && $videogames->count() > 0)
            <div class="list-group">
                @foreach ($videogames as $game)
                    <div class="list-group-item">
                        {{-- Access game attributes via $game->attribute --}}
                        {{-- Access pivot data via $game->pivot->attribute --}}
                        <h5>{{ $game->name }}</h5>
                        <p class="mb-1">
                            <span class="badge bg-primary">Status: {{ $game->pivot->status ?? 'N/A' }}</span>
                            @if($game->pivot->rating)
                                <span class="badge bg-warning text-dark">Rating: {{ $game->pivot->rating }}/5</span>
                            @endif
                            @if($game->pivot->playtime_hours)
                                <span class="badge bg-info text-dark">Playtime: {{ $game->pivot->playtime_hours }} hrs</span>
                            @endif
                        </p>
                        @if($game->pivot->comment)
                            <p class="mb-1 fst-italic">Comment: {{ $game->pivot->comment }}</p>
                        @endif
                        <small class="text-muted">Added:
                            {{ $game->pivot->created_at ? $game->pivot->created_at->format('Y-m-d') : 'N/A' }}</small>
                    </div>
                    <div> {{-- Div for actions --}}
                        {{-- Add Edit button/link here later --}}
                        {{-- <a href="{{ route('videogames.edit', $game->id) }}" class="btn btn-secondary btn-sm">Edit</a> --}}
                        {{-- Remove button form --}}
                        <form action="{{ route('my-videogames.destroy', $game->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to remove {{ addslashes($game->name) }} from your collection?');">
                            @csrf
                            @method('DELETE') {{-- Method Spoofing --}}
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                    </div>
                @endforeach
            </div>
        @else
            <p>You haven't added any videogames to your collection yet.</p>
            <a href="{{ route('videogames.search') }}" class="btn btn-primary">Search for games to add!</a>
        @endif
    </div>
@endsection