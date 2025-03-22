@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Add New Book</h1>
        <form action="{{ route('books.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" class="form-control" id="author" name="author" required>
            </div>
            <div class="form-group">
                <label for="isbn">ISBN</label>
                <input type="text" class="form-control" id="isbn" name="isbn" required>
            </div>
            <div class="form-group">
                <label for="language">Language</label>
                <input type="text" name="language" id="language" class="form-control">
            </div>

            <!-- Pages -->
            <div class="form-group">
                <label for="pages">Pages</label>
                <input type="number" name="pages" id="pages" class="form-control">
            </div>

            <!-- Genre -->
            <div class="form-group">
                <label for="genre">Genre</label>
                <input type="text" name="genre" id="genre" class="form-control">
            </div>

            <!-- Cover Image -->
            <div class="form-group">
                <label for="cover_image">Cover Image URL</label>
                <input type="url" name="cover_image" id="cover_image" class="form-control">
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4"></textarea>
            </div>

            <!-- Publisher -->
            <div class="form-group">
                <label for="publisher">Publisher</label>
                <input type="text" name="publisher" id="publisher" class="form-control">
            </div>

            <!-- Publication Year (This Publisher) -->
            <div class="form-group">
                <label for="publication_year_this_publisher">Publication Year (This Publisher)</label>
                <input type="number" name="publication_year_this_publisher" id="publication_year_this_publisher"
                    class="form-control">
            </div>

            <!-- Publication Year (Original) -->
            <div class="form-group">
                <label for="publication_year_original">Publication Year (Original)</label>
                <input type="number" name="publication_year_original" id="publication_year_original" class="form-control">
            </div>

            <!-- Valoration -->
            <div class="form-group">
                <label for="valoration">Valoration (1-5)</label>
                <input type="number" name="valoration" id="valoration" class="form-control" min="1" max="5">
            </div>

            <!-- Comments -->
            <div class="form-group">
                <label for="comments">Comments</label>
                <textarea name="comments" id="comments" class="form-control" rows="4"></textarea>
            </div>

            <!-- Format -->
            <div class="form-group">
                <label for="format">Format</label>
                <input type="text" name="format" id="format" class="form-control">
            </div>

            <!-- Tags -->
            <div class="form-group">
                <label for="tags">Tags</label>
                <input type="text" name="tags" id="tags" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Add Book</button>
        </form>
    </div>
@endsection