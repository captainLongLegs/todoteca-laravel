@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Acces main book details via $book, pivot data via $pivotData --}}
        <h1>Edit Collection Details for: {{ $book->title }}</h1>
        <p><em>by {{ $book->author ?? 'Unknown Author' }}</em></p>

        {{-- Display book cover image if available --}}
        @if ($book->cover_image)
            <div class="mb-3">
                <img src="{{ $book->cover_image }}" alt="{{ $book->title }} Cover" class="img-fluid rounded shadow-sm"
                    style="max-height:200px;">
            </div>
        @endif

        {{-- Form posts to the update route, using PATCH method --}}
        <form action="{{ route('my-books.update', $book->id) }}" method="POST">
            @csrf
            @method('PATCH') {{-- Or PUT, matching the route definition --}}

            {{-- Status Selection --}}
            <div class="form-group mb-3">
                <label for="status" class="form-label">Status: </label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    {{-- Define possible statuses --}}
                    @php $statuses = ['to-read', 'reading', 'read']; @endphp
                    @foreach($statuses as $statusOption)
                        <option value="{{ $statusOption }}" {{ old('status', $pivotData->status) == $statusOption ? 'selected' : '' }}>
                            {{ Str::of($statusOption)->replace('-', ' ')->title() }}
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

            {{-- Submit and Cancel Buttons --}}
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update Collection Item</button>
                <a href="{{ route('my-books') }}" class="btn btn-secondary">Cancel</a>  
            </div>
        </form>
    </div>

@endsection