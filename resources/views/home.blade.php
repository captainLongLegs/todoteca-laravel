@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('TODOTECA') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @auth
                            <p>{{ __('You are logged in!') }}</p>
                        @endauth
                        <p>Welcome to the cultural app TODOTECA. With TODOTECA you can search, rate and comment your favourite cultural artifacts!</p>
                        <p>If you love Books, here's what you can do:</p>
                        <ul>
                            <li>View all books in our user's local database in the <a href="{{ route('books.index') }}">Books</a> section.</li>
                            <li>Add a new book manually to the database and your personal collection in the <a href="{{ route('books.create') }}">Add Book Manually</a> section.</li>
                            <li>View your personal book collection in the <a href="{{ route('my-books') }}">My
                                    Collection</a> section.</li>
                        </ul>
                        <p>Also, if you love Videogames, here's what you can do:</p>
                        <ul>
                            <li>View all videogames in our user's local database in the <a href="{{ route('videogames.index') }}">Videogames</a> section.</li>
                            <li>Add a new videogame manually to the database and your personal collection in the <a href="{{ route('videogames.create') }}">Add Videogame Manually</a> section.</li>
                            <li>View your personal videogame collection in the <a href="{{ route('my-videogames') }}">My
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