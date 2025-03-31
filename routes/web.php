<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserBookController;

//Home Route
Route::get('/', function () {
    return view('home');
})->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// API Search Route
Route::get('/books/search', [BookController::class, 'search'])->name('books.search');
Route::get('/books/search/results', [BookController::class, 'searchResults'])->name('books.search.results');

//Book routes
Route::resource('books', BookController::class);
Route::get('/my-collection', function() {
    return view('books.my-collection');
})->name('my-collection')->middleware('auth');

//UserBook routes
Route::post('/books/{book}/add-to-collection', [UserBookController::class, 'store'])
    ->name('user-books.store')
    ->middleware('auth');
Auth::routes();



// Testing Route
//Route::get('/books/search', function() {
//    dd('Simplified route test');
//})->name('books.search');