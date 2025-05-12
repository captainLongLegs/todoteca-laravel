@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>My Videogame Collection</h1>
        <p>This page displays the videogames you have added to your collection.</p>

        {{-- Session Messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif
        @if (session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        {{-- Filter Form Start --}}
        <div class="card mb-4">
            <div class="card-header">Filter Collection</div>
            <div class="card-body">
                <form action="{{ route('my-videogames')}}" method="GET" class="row g-3 align-items-end">
                    {{-- Filter by Status --}}
                    <div class="col-md-3">
                        <label for="filter_status" class="form-label">Status: </label>
                        <select name="status" id="filter_status" class="form-select form-select-sm">
                            <option value="">--Any Status</option>
                            <option value="wishlist" {{ request('status') == 'wishlist' ? 'selected' : '' }}>Wishlist</option>
                            <option value="backlog" {{ request('status') == 'backlog' ? 'selected' : '' }}>Backlog</option>
                            <option value="playing" {{ request('status') == 'playing' ? 'selected' : '' }}>Playing</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                        </select>
                    </div>

                    {{-- Filter by Rating --}}
                    <div class="col-md-2">
                        <label for="filter_rating" class="form-label">Min. Rating:</label>
                        <select name="rating" id="filter_rating" class="form-select form-select-sm">
                            <option value="">-- Any Rating --</option>
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }}
                                    Star{{ $i > 1 ? 's' : '' }} & Up</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Filter by Platform (Requires passing platforms from controller) --}}
                    <div class="col-md-3">
                        <label for="filter_platform" class="form-label">Platform:</label>
                        <select name="platform" id="filter_platform" class="form-select form-select-sm">
                            <option value="">-- Any Platform --</option>
                            @isset($platforms) {{-- Check if $platforms exists --}}
                                @foreach ($platforms as $platform)
                                    <option value="{{ $platform->id }}" {{ request('platform') == $platform->id ? 'selected' : '' }}>
                                        {{ $platform->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    {{-- Filter by Genre (Requires passing genres from controller) --}}
                    <div class="col-md-3">
                        <label for="filter_genre" class="form-label">Genre:</label>
                        <select name="genre" id="filter_genre" class="form-select form-select-sm">
                            <option value="">-- Any Genre --</option>
                            @isset($genres) {{-- Check if $genres exists --}}
                                @foreach ($genres as $genre)
                                    <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>
                                        {{ $genre->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm me-2">Filter</button>
                        <a href="{{ route('my-videogames') }}" class="btn btn-secondary btn-sm" title="Clear Filters">X</a>
                    </div>
                </form>
            </div>
        </div>
        {{-- FILTER FORM END --}}

        {{-- Videogame Collection Display --}}
        @if (isset($videogames) && $videogames->count() > 0)
            <div class="row">
                @foreach ($videogames as $game)
                    <div class="col-md-6 col-lg4 mb-4" id="game-{{ $game->id }}">
                        <div class="card h-100">
                            {{-- Image Display --}}
                            @if ($game->background_image)
                                <img src="{{ $game->background_image }}" alt="{{ $game->name }} Cover" class="card-img-top"
                                    style="height: 180px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center"
                                    style="height: 180px;">
                                    <span>No Cover Image</span>
                                </div>
                            @endif

                            {{-- Access game attributes via $game->attribute --}}
                            {{-- Access pivot data via $game->pivot->attribute --}}
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $game->name }}</h5>
                                <p class="card-text text-muted small mb-1"><em>{{ $game->developer ?? 'Unknown Developer' }}</em>
                                    ({{ $game->released ? $game->released->format('Y') : 'N/A' }})
                                </p>

                                {{-- Pivot Data Badges --}}
                                <p class="mb-2">
                                    <span class="badge bg-primary me-1">{{ Str::title($game->pivot->status) ?? 'N/A' }}</span>
                                    @if($game->pivot->rating)
                                        <span class="badge bg-warning text-dark me-1">★ {{ $game->pivot->rating }}/5</span>
                                    @endif
                                    @if($game->pivot->playtime_hours)
                                        <span class="badge bg-info text-dark">🕒 {{ $game->pivot->playtime_hours }}
                                            hrs</span>
                                    @endif
                                </p>

                                {{-- Platforms & Genres Display --}}
                                <div class="mb-2 small">
                                    @if($game->platforms->isNotEmpty())
                                        <strong>Platforms:</strong> {{ $game->platforms->pluck('name')->implode(', ') }} <br>
                                    @endif
                                    @if($game->genres->isNotEmpty())
                                        <strong>Genres:</strong> {{ $game->genres->pluck('name')->implode(', ') }}
                                    @endif
                                </div>

                                {{-- Comments --}}
                                @if($game->pivot->comment)
                                    <p class="mb-1 fst-italic small coment-preview" data-bs-toggle="tooltip"
                                        title="{{ Str::limit($game->pivot->comment, 300) }}"><strong>Comment:
                                        </strong>{{ Str::limit($game->pivot->comment, 100) }}</p>
                                @endif
                                <small class="text-muted mt-auto">Added:
                                    {{ $game->pivot->created_at ? $game->pivot->created_at->format('Y-m-d') : 'N/A' }}</small>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="card-footer bg-light btn-group border-top-0">
                            {{-- Edit button/link --}}
                            <a href="{{ route('my-videogames.edit', $game->id) }}" class="btn btn-secondary btn-sm">Edit</a>

                            {{-- Remove button form --}}
                            <form action="{{ route('my-videogames.destroy', $game->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Are you sure you want to remove {{ addslashes($game->name) }} from your collection?');">
                                @csrf
                                @method('DELETE') {{-- Method Spoofing --}}
                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- End of Videogame Collection Display --}}

            {{-- Pagination Links --}}
            <div class="d-flex justify-content-center mt-4">
                {{-- Make sure you use paginate() in the controller for this to work --}}
                {{ $videogames->appends(request()->query())->links() }}
            </div>

        @else
            <p>
                @if(request()->hasAny(['status', 'rating', 'platform', 'genre']))
                    No games found matching your current filters.
                @else
                    You haven't added any videogames to your collection yet.
                @endif
            </p>
            <a href="{{ route('videogames.search') }}" class="btn btn-primary">Search for games to add!</a>
        @endif
    </div>
@endsection

@push('scripts') {{-- Optional: Add script section for Tooltips if not already in app layout --}}
    <script>
        // Initialize Bootstrap Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endpush