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
    Route::post('/store', [BookController::class, 'store'])->name('store');
    Route::post('/store-from-search', [BookController::class, 'storeFromSearch']) // Store a book from API search results
        ->name('store-from-search')
        ->middleware('auth');

});

// === User's Book Collection Routes ===
Route::middleware('auth')->group(function () { // Group routes that require authentication

    Route::get('/my-books', [UserBookController::class, 'index']) // List user's books
        ->name('my-books');
    
    Route::post('/books/{book}/add-to-collection', [UserBookController::class, 'addLocalBookToCollection']) // Add a book to user's collection
        ->name('books.add-local-to-collection');

    Route::get('/my-books/{book}/edit', [UserBookController::class, 'edit']) // Edit book in user's collection
        ->name('my-books.edit');

    Route::match(['put', 'patch'], '/my-books/{book}', [UserBookController::class, 'update']) // Update book in user's collection
        ->name('my-books.update');
    
    Route::delete('/my-books/{book}', [UserBookController::class, 'destroy']) // Remove book from user's collection
        ->name('my-books.destroy');
    

});



// === Videogames routes ===
Route::prefix('videogames')->name('videogames.')->group(function () {
    Route::get('/', [VideogameController::class, 'index'])->name('index'); // List local videogames
    Route::get('/search', [VideogameController::class, 'search'])->name('search'); // API Search Form
    Route::get('/search/results', [VideogameController::class, 'searchResults'])->name('search.results'); // API Search Results
    Route::get('/create', [VideogameController::class, 'create'])->name('create'); // Form to add manually a new videogame

    Route::post('/', [VideogameController::class, 'store'])->name('store'); // Store a manually added videogame
    Route::post('/{videogame}/add-to-collection', [VideogameController::class, 'addLocalToCollection']) // Add a local videogame to the user's collection
        ->middleware('auth') 
        ->name('add-local-to-collection'); 

    Route::post('/store-from-search', [VideogameController::class, 'storeFromSearch']) // Store a videogame from API search results
        ->name('store-from-search')
        ->middleware('auth');
});

// === User's Videogame Collection Routes ===
Route::middleware('auth')->group(function () { // Group routes that require authentication

    Route::get('/my-videogames', [VideogameController::class, 'myCollection']) // List user's videogames
        ->name('my-videogames');

    Route::get('/my-videogames/{videogame}/edit', [VideogameController::class, 'editCollectionItem'])
        ->name('my-videogames.edit'); // Edit videogame in user's collection

    Route::match(['put', 'patch'], '/my-videogames/{videogame}', [VideogameController::class, 'updateCollectionItem']) // Update videogame in user's collection
        ->name('my-videogames.update');
    
    Route::delete('/my-videogames/{videogame}', [VideogameController::class, 'removeFromCollection']) // Remove videogame from user's collection
        ->name('my-videogames.destroy');
});
// Testing Route
//Route::get('/books/search', function() {
//    dd('Simplified route test');
//})->name('books.search');