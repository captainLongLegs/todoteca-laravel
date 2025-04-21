@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Add New Videogame</h1>
        <form action="{{ route('videogames.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="name">Title</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group mb-3">
                <label for="developer">Developer</label>
                <input type="text" class="form-control" id="developer" name="developer" required>
            </div>
            <div class="form-group mb-3">
                <label for="publisher">Publisher</label>
                <input type="text" class="form-control" id="publisher" name="publisher" required>
            </div>
            <div class="form-group mb-3">
                <label for="released">Release date</label>
                <input type="date" name="released" id="released" class="form-control">
            </div>
            <div class="form-group mb-3">
                <label for="background_image">Cover Image URL</label>
                <input type="url" name="background_image" id="background_image" class="form-control">
            </div>
            <div class="form-group mb-3">
                <label for="description">Description</label>
                <input type="text" name="description" id="description" class="form-control">
            </div>
            <div class="form-group mb-3">
                <label for="age_rating">Age Rating</label>
                <input type="text" name="age_rating" id="age_rating" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Add Videogame</button>
        </form>
    </div>
@endsection