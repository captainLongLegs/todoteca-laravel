<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserBookController;

Route::get('/', function () {
    return view('home');
})->name('home');

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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');