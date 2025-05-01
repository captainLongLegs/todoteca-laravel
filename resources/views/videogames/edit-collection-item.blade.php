@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Acces main game details via $game, pivot data via $pivotData --}}
        <h1>Edit Collection Details for: {{ $videogame->name }}</h1>

        {{-- Display game cove image if available --}}
        @if ($videogame->background_image)
            <div class="mb-3">
                <img src="{{ $videogame->background_image }}" alt="{{ $videogame->name }} Cover" class="img-fluid rounded shadow-sm" style="max-height:200px;">
            </div>
        @endif

        {{-- Form posts to the update route, using PATCH method --}}
        <form action="{{ route('my-videogames.update', $videogame->id) }}" method="POST">
            @csrf
            @method('PATCH') {{-- Or PUT, matching the route definition --}}

            {{-- Status Selection --}}
            <div class="form-group mb-3">
                <label for="status" class="form-label">Status: </label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    {{-- Define possible statuses --}}
                    @php $statuses = ['whishlist', 'backlog', 'playing', 'completed', 'dropped']; @endphp
                    @foreach($statuses as $statusOption)
                    <option value="{{ $statusOption }}" {{ old('status', $pivotData->status) == $statusOption ? 'selected' : '' }}>
                        {{ ucfirst($statusOption) }}
                    </option>
                    @endforeach
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Rating Input --}}
            <div class="form-group mb-3">
                <label for="rating" class="form-label fw-bold">Your Rating (1-5): </label>
                <input type="number" name="rating" id="rating" class="form-control @error('rating') is-invalid @enderror"
                    min="1" max="5" step="1" value="{{ old('rating', $pivotData->rating) }}">   
                    @error('rating')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>

            {{-- Comment Text Area --}}
            <div class="form-group mb-3">
                <label for="comment" class="form-label fw-bold">Your Comment: </label>
                <textarea name="comment" id="comment" class="form-control @error('comment') is-invalid @enderror"
                    rows="4">{{ old('comment', $pivotData->comment) }}</textarea>
                @error('comment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Playtime Input --}}
            <div class="form-group mb-3">
                <label for="playtime_hours" class="form-label fw-bold">Playtime (in hours): </label>
                <input type="number" name="playtime_hours" id="playtime_hours" class="form-control @error('playtime_hours') is-invalid @enderror"
                    min="0" step="1" value="{{ old('playtime_hours', $pivotData->playtime_hours) }}">   
                    @error('playtime_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
            </div>

            {{-- Submit and Cancel Buttons --}}
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update Collection Item</button>
                <a href="{{ route('my-videogames') }}" class="btn btn-secondary">Cancel</a>
            </div>
                    
        </form>

@endsection