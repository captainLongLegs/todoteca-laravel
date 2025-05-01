@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Videogames (Local DB)</h1>
            {{-- Link to manual add page --}}
            <a href="{{ route('videogames.create') }}" class="btn btn-primary">Add New Videogame</a>
        </div>

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


        {{-- Check if using pagination or simple collection --}}
        {{-- If controller uses paginate(), $videogames->isEmpty() works. If get(), use count() --}}
        @php
            // Determine check method based on variable type
            $isEmptyCheck = ($videogames instanceof \Illuminate\Pagination\LengthAwarePaginator || $videogames instanceof \Illuminate\Support\Collection)
                ? $videogames->isEmpty()
                : empty($videogames);
        @endphp

        @if ($isEmptyCheck)
            <div class="alert alert-info">No videogames found in the local database.</div>
        @else
            <div class="row">
                @foreach ($videogames as $game)
                    <div class="col-md-6 col-lg-4 mb-4"> {{-- Adjusted columns --}}
                        <div class="card h-100">
                            @if ($game->background_image)
                                <img src="{{ $game->background_image }}" class="card-img-top" alt="{{ $game->name }} Cover"
                                    style="height: 250px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center"
                                    style="height: 250px;">
                                    <span>No Cover Image</span>
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $game->name }}</h5>
                                <p class="card-text text-muted"><em>{{ $game->developer ?? 'Unknown Developer' }}</em></p>

                                {{-- Add to Collection Form --}}
                                @auth
                                    <form action="{{ route('videogames.add-local-to-collection', $game->id) }}" method="POST"
                                        class="mt-auto pt-3 border-top"> {{-- Push form down, add border --}}
                                        @csrf
                                        {{-- Status Select --}}
                                        <div class="form-group mb-2">
                                            <label for="status_{{ $game->id }}" class="form-label fw-bold small">Status:</label>
                                            <select name="status" id="status_{{ $game->id }}" class="form-select form-select-sm"
                                                required>
                                                @php $statuses = ['wishlist', 'backlog', 'playing', 'completed', 'dropped']; @endphp
                                                @foreach($statuses as $statusOption)
                                                    <option value="{{ $statusOption }}" {{ $statusOption == 'backlog' ? 'selected' : '' }}>
                                                        {{ ucfirst($statusOption) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- Rating Input --}}
                                        <div class="form-group mb-2">
                                            <label for="rating_{{ $game->id }}" class="form-label fw-bold small">Rating (1-5):</label>
                                            <input type="number" name="rating" id="rating_{{ $game->id }}"
                                                class="form-control form-control-sm" min="1" max="5" step="1">
                                        </div>
                                        {{-- Comment Textarea --}}
                                        <div class="form-group mb-2">
                                            <label for="comment_{{ $game->id }}" class="form-label fw-bold small">Comment:</label>
                                            <textarea name="comment" id="comment_{{ $game->id }}" class="form-control form-control-sm"
                                                rows="2"></textarea>
                                        </div>
                                        {{-- Playtime Input (Optional) --}}
                                        <div class="form-group mb-2">
                                            <label for="playtime_hours_{{ $game->id }}" class="form-label fw-bold small">Playtime
                                                (Hours):</label>
                                            <input type="number" name="playtime_hours" id="playtime_hours_{{ $game->id }}"
                                                class="form-control form-control-sm" min="0" step="1">
                                        </div>
                                        {{-- Submit Button --}}
                                        <button type="submit" class="btn btn-success btn-sm w-100 mt-2">Add to My Collection</button>
                                    </form>
                                @else {{-- Show login prompt if not logged in --}}
                                    <p class="text-muted mt-auto pt-3 border-top"><a href="{{ route('login') }}">Log in</a> to add to
                                        your collection.</p>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="mt-4">
                {{-- Ensure controller method uses paginate() for this to work --}}
                @if ($videogames instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $videogames->links() }}
                @endif
            </div>

        @endif {{-- End isEmptyCheck --}}
    </div>
@endsection