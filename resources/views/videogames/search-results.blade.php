@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Videogame Search Results</h1>

        {{-- Display the search query if available --}}
        @if(isset($query) && $query)
            <p class="lead">Showing results for: <strong>"{{ $query}}"</strong></p>
        @endif

        {{-- Display any error messages passed from the controller --}}
        @isset($error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endisset

        {{-- Check if the $videogames variable exists and is not empty --}}
        @if (isset($videogames) && count($videogames) > 0)
            <div class="list-group"> {{-- Using list-group for simple layout --}}
                @foreach ($videogames as $game)
                {{-- DD Checking the answer from the API --}}
                {{-- dd($game) --}}
                {{-- END DD --}}    
                <div class="list-group-item list-group-item-action mb-3">
                        <div class="row g-3">

                            {{-- Game Cover Image --}}
                            <div class="col-md-2 text-center">
                                @if (!empty($game['background_image']))
                                    <img src="{{ $game['background_image'] }}" alt="{{ $game['name'] }} Cover" class="img-fluid rounded"
                                        stule="max-height: 150px;">
                                @else
                                    <div class="bg-secondary rounded text-white d-flex align-items-center justify-content-center"
                                        style="height: 150px width: 100%;">
                                        <span>No Image Available</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Game Details --}}
                            <div class="col-md-6">
                                <h5 class="mb-1">
                                    {{ $game['name'] ?? 'No Title Provided'}}
                                </h5>
                                <p class="mb-1">
                                    <small>
                                        <strong>Released: </strong> {{ $game['released'] ?
                        \Carbon\Carbon::parse($game['released'])->format('d m y') : 'N/A' }}
                                    </small>
                                </p>
                                <p class="mb-1">
                                    <small>
                                        <strong>Platforms: </strong>
                                        @if (!empty($game['platforms']))
                                            {{-- Loop through platforms objects, get the name --}}
                                            {{ implode(', ', array_column(array_column($game['platforms'], 'platform'), 'name')) }}
                                        @else
                                            N/A
                                        @endif
                                    </small>
                                </p>
                            </div>

                            {{-- Add to Collection Form --}}
                            <div class="col-md-4">
                                {{-- This form POSTs to the route that handles storing the videogame in the user's collection --}}
                                {{-- Ensure you are logged in to see this form --}}
                                @auth
                                    <form action="{{ route('videogames.store-from-search') }}" method="POST" class="mt-2">
                                        @csrf
                                        {{-- Hidden fields to pass game data needed for storing
                                        
                                        --}}
                                        {{-- We need enough info to uniquely identify and save the game --}}
                                        <input type="hidden" name="api_id" value="{{ $game['id'] }}"> {{-- RAWG ID --}}
                                        <input type="hidden" name="name" value="{{ $game['name'] ?? 'Unknown title' }}">
                                        <input type="hidden" name="slug" value="{{ $game['slug'] ?? '' }}">
                                        <input type="hidden" name="background_image" value="{{ $game['background_image'] ?? '' }}">
                                        <input type="hidden" name="released" value="{{ $game['released'] ?? '' }}">
                                        {{-- Pass platform names as a comma-separated string --}}
                                        <input type="hidden" name="platforms_string"
                                            value="{{ !empty($game['platforms']) ? implode(', ', array_column(array_column($game['platforms'], 'platform'), 'name')) : '' }}">
                                        {{-- Pass genre names as a comma-separated string --}}
                                        <input type="hidden" name="genres_string"
                                            value="{{ !empty($game['genres']) ? implode(', ', array_column($game['genres'], 'name')) : '' }}">

                                        {{-- User-specific collection data --}}
                                        <div class="mb-2">
                                            <label for="status_{{ $game['id'] }}" class="form-label fw-bold">Status:</label>
                                            <select name="status" id="status_{{ $game['id'] }}" class="form-select form-select-sm"
                                                required>
                                                <option value="wishlist">Wishlist</option>
                                                <option value="backlog" selected>Backlog</option> {{-- Default --}}
                                                <option value="playing">Playing</option>
                                                <option value="completed">Completed</option>
                                                <option value="dropped">Dropped</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label for="rating_{{ $game['id'] }}" class="form-label fw-bold">Rating (1-5):</label>
                                            <input type="number" name="rating" id="rating_{{ $game['id'] }}"
                                                class="form-control form-control-sm" min="1" max="5" step="1">
                                        </div>
                                        <div class="mb-2">
                                            <label for="comment_{{ $game['id'] }}" class="form-label fw-bold">Comment:</label>
                                            <textarea name="comment" id="comment_{{ $game['id'] }}" class="form-control form-control-sm"
                                                rows="2"></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-success btn-sm w-100">Add to My Collection</button>
                                    </form>
                                @else
                                    <p class="text-muted mt-3"><a href="{{ route('login') }}">Log in</a> to add games to your
                                        collection.</p>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif (!isset($error)) {{-- Only show 'No results' if there wasn't an API error --}}
            <p>No videogames found matching your query "{{ $query ?? '' }}". Please try different search terms.</p>
        @endif
    </div>
@endsection