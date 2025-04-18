<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserBookController;
use App\Http\Controllers\VideogameController;
use App\Http\Controllers\HomeController;

// === Home Route ===
Route::get('/', function () {
    return view('home');
})->name('home');

Auth::routes();

// === Book Routes ===
Route::prefix('books')->name('books.')->group(function () {
    Route::get('/', [BookController::class, 'index'])->name('index'); // List local books
    Route::get('/create', [BookController::class, 'create'])->name('create'); // Form to add manually a new book
    Route::post('/', [BookController::class, 'store'])->name('store'); // Store a manually added book
    Route::get('/search', [BookController::class, 'search'])->name('search'); // API Search Form
    Route::get('/search/results', [BookController::class, 'searchResults'])->name('search.results'); // API Search Results
    // Route::get('/{book}', [BookController::class, 'show'])->name('show'); // Show book details (future implementation)
    // Route::get('/{book}/edit', [BookController::class, 'edit'])->name('edit'); // Edit book (future implementation)
    // Route::put('/{book}', [BookController::class, 'update'])->name('update'); // Update book data(future implementation)
    // Route::delete('/{book}', [BookController::class, 'destroy'])->name('destroy'); // Delete book from ? (future implementation)

    Route::post('/store-from-search', [BookController::class, 'storeFromSearch']) // Store a book from API search results
        ->name('store-from-search')
        ->middleware('auth');

});

// === User's Book Collection Routes ===
Route::get('/my-books', [UserBookController::class, 'index']) // List user's books
    ->name('my-books')
    ->middleware('auth');

Route::post('/books/{book}/add-to-collection', [UserBookController::class, 'store'])
    ->name('user-books.store')
    ->middleware('auth');

// === Videogames routes ===
Route::prefix('videogames')->name('videogames.')->group(function () {
    Route::get('/', [VideogameController::class, 'index'])->name('index'); // List local videogames
    Route::get('/search', [VideogameController::class, 'search'])->name('search'); // API Search Form
    Route::get('/search/results', [VideogameController::class, 'searchResults'])->name('search.results'); // API Search Results

    Route::post('/store-from-search', [VideogameController::class, 'storeFromSearch']) // Store a videogame from API search results
        ->name('store-from-search')
        ->middleware('auth');
});

// === User's Videogame Collection Routes ===
Route::get('/my-videogames', [VideogameController::class, 'index'])
    ->name('my-videogames')
    ->middleware('auth');

// Testing Route
//Route::get('/books/search', function() {
//    dd('Simplified route test');
//})->name('books.search');