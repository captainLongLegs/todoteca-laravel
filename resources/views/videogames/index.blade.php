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


        {{-- Search form and sorting container --}}
        <div class="card mb-4">
            <div class="card-header">Search & Sort Videogames by:</div>
            <div class="card-body">
                <form action="{{ route('videogames.index') }}" method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="Search by name, developer, platform..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if(request('search'))
                            <a href="{{ route('videogames.index', array_filter(request()->except('search', 'page'))) }}"
                                class="btn btn-secondary">Clear Search</a>
                        @endif
                    </div>
                </form>

                <div class="d-flex justify-content-start flex-wrap gap-2 small">
                    <span class="me-2 align-self-center">Sort by:</span>
                    @php
                        function videogame_sort_link($column, $label, $currentSortBy, $currentSortDirection)
                        {
                            $searchParam = request()->has('search') ? ['search' => request('search')] : [];
                            $newSortDirection = ($currentSortBy == $column && $currentSortDirection == 'asc') ? 'desc' : 'asc';
                            $url = request()->fullUrlWithQuery(array_merge($searchParam, ['sort_by' => $column, 'sort_dir' => $newSortDirection]));
                            $arrow = '';
                            if ($currentSortBy == $column) {
                                $arrow = $currentSortDirection == 'asc' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>';
                            }
                            $activeClass = $currentSortBy == $column ? 'fw-bold' : '';
                            return '<a href="' . e($url) . '" class="btn btn-outline-secondary btn-sm ' . $activeClass . '">' . e($label) . ' ' . $arrow . '</a>';
                        }

                        $sortBy = request('sort_by', 'created_at');
                        $sortDir = request('sort_dir', 'desc');
                    @endphp
                    {!! videogame_sort_link('name', 'Name', $sortBy, $sortDir) !!}
                    {!! videogame_sort_link('developer', 'Developer', $sortBy, $sortDir) !!}
                    {!! videogame_sort_link('released', 'Release Date', $sortBy, $sortDir) !!}
                    {!! videogame_sort_link('created_at', 'Date Added', $sortBy, $sortDir) !!}
                </div>
            </div>
        </div>

        @php
            // Determine check method based on variable type
            $isEmptyCheck = ($videogames instanceof \Illuminate\Pagination\LengthAwarePaginator || $videogames instanceof \Illuminate\Support\Collection)
                ? $videogames->isEmpty()
                : empty($videogames);
        @endphp

        @if ($isEmptyCheck)
            <div class="alert alert-info">
                @if(request('search'))
                    No videogames found matching your search term: "{{ request('search') }}".
                @else
                    No videogames found in the local database.
                @endif
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($videogames as $game)
                    <div class="col" id="videogame-{{$game->id}}">
                        <div class="card h-100">
                            @if ($game->background_image)
                                <img src="{{ $game->background_image }}" class="card-img-top" alt="{{ $game->name }} Cover"
                                    style="height: 180px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center"
                                    style="height: 180px; border-bottom: 1px solid #eee;">
                                    <span class="text-muted small">No Cover</span>
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $game->name }}</h5>
                                <p class="card-text text-muted small mb-1"><em>{{ $game->developer ?? 'Unknown Developer' }}</em>
                                    {{ $game->released ? '(' . $game->released->format('Y') . ')' : '' }}
                                </p>

                                <div class="small mb-2">
                                    @if($game->platforms->isNotEmpty())
                                        <strong>Platforms</strong> {{ $game->platforms->pluck('name')->implode('. ') }}
                                    @endif
                                    @if($game->genres->isNotEmpty())
                                        <strong>Genres</strong> {{ $game->genres->pluck('name')->implode('. ') }}
                                    @endif
                                </div>

                                {{-- Add to Collection Form --}}
                                <div class="mt-auto">
                                @auth
                                    <form action="{{ route('videogames.add-local-to-collection', $game->id) }}" method="POST"
                                        class="pt-2 border-top">

                                        @csrf
                                        {{-- Status Select --}}
                                        <div class="mb-2">
                                            <label for="status_{{ $game->id }}" class="form-label fw-bold small visually-hidden">Status:</label>
                                            <select name="status" id="status_{{ $game->id }}" class="form-select form-select-sm"
                                                required>
                                                @php $statuses = ['wishlist', 'backlog', 'playing', 'completed', 'dropped']; 
                                                @endphp
                                                @foreach($statuses as $statusOption)
                                                    <option value="{{ $statusOption }}" {{ $statusOption == 'backlog' ? 'selected' : '' }}>
                                                        {{  Str::of($statusOption)->replace('-', ' ')->title()}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Rating Input --}}
                                        <div class="form-group mb-2">
                                            <label for="rating_{{ $game->id }}" class="form-label fw-bold small">Rating
                                                (1-5):</label>
                                            <input type="number" name="rating" id="rating_{{ $game->id }}"
                                                class="form-control form-control-sm" min="1" max="5" step="1">
                                        </div>

                                        {{-- Comment Textarea --}}
                                        <div class="form-group mb-2">
                                            <label for="comment_{{ $game->id }}" class="form-label fw-bold small">Comment:</label>
                                            <textarea name="comment" id="comment_{{ $game->id }}" class="form-control form-control-sm"
                                                rows="2"></textarea>
                                        </div>

                                        {{-- Playtime Input --}}
                                        <div class="form-group mb-2">
                                            <label for="playtime_hours_{{ $game->id }}" class="form-label fw-bold small">Playtime
                                                (Hours):</label>
                                            <input type="number" name="playtime_hours" id="playtime_hours_{{ $game->id }}"
                                                class="form-control form-control-sm" min="0" step="1">
                                        </div>

                                        <button type="submit" class="btn btn-success btn-sm w-100 mt-2">Add to My Collection</button>
                                    </form>
                                @else
                                    <p class="text-muted mt-auto pt-3 border-top"><a href="{{ route('login') }}">Log in</a>to add to your collection.</p>
                                @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="mt-4 d-flex justify-content-center">
                                @if ($videogames instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $videogames->links() }}
                @endif
            </div>

        @endif 
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush