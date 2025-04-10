@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Todoteca') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @auth
                            <p>{{ __('You are logged in!') }}</p>
                        @endauth
                        <p>This is the homepage of the Book App. Here's what you can do:</p>
                        <ul>
                            <li>View all books in the <a href="{{ route('books.index') }}">Books</a> section.</li>
                            <li>Add a new book in the <a href="{{ route('books.create') }}">Add Book</a> section.</li>
                            <li>View your personal book collection in the <a href="{{ route('my-books') }}">My
                                    Collection</a> section.</li>
                        </ul>

                        @guest
                            <p>To get started, please <a href="{{ route('login') }}">login</a> or <a
                                    href="{{ route('register') }}">register</a>.</p>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection