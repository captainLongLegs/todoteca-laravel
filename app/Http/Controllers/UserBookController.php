<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class UserBookController extends Controller
{
    /**
     * Display the authenticated user's book collection.
     */
    public function index()
    {
        // 1. Get the authenticated user
        $user = Auth::user();

        // 2. Check if a user is actually logged in (though middleware should prevent this).
        if (!$user) {
            return redirect()->route('login');
        }

        // 3. Fetch the books related to this user
        $books = $user->books()
            ->withPivot('status', 'rating', 'comment')
            ->orderByPivot('created_at', 'desc')
            ->get();

        // 4. Return the view with the user's books
        return view('books.my-collection', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /* 
    * DEPRECATED: This method is not used anymore, as we are using the addLocalBookToCollection method to add books to the collection.
    public function store(Request $request, Book $book)
    {
        $user = Auth::user();
        // Validate the data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:20',
            'status' => ['required', Rule::in(['to-read','reading','read', 'on-hold', 'abandoned'])],
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $book = Book::firstOrCreate(
            ['isbn' => $validatedData['isbn']], //Search by ISBN if available
            [
                'title' => $validatedData['title'],
                'author' => $validatedData['author'],
            ]
        );

        // Check if the book already exists in the user's collection
        if (Auth::user()->books()->where('book_id', $book->id)->exists()) {
            return redirect()->back()->with('error', 'This book is already in your collection');
        }

        // Prepare the pivot data
        $pivotData = [
            'status' => $validatedData['status'],
            'rating' => $validatedData['rating'] ?? null,
            'comment' => $validatedData['comment'] ?? null,
        ];
        
        // Attach the book to the user with metadata
        Auth::user()->books()->attach($book->id, $pivotData);

        // Redirect to the user's collection
        return redirect()
            ->route('my-books')
            ->with('success', 'Book added to your collection');

        // Redirect to the book page
        // return redirect()->route('books.index')->with('success', 'Book added to your collection');
    }
 */

    /**
     * Show the form for editing the user's collection details for a specific book
     * Route: my-books.edit (GET /my-books/{book}/edit)
     * 
     * @param Book $book Route model binding
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Book $book) // Here we are using the standard edit name, not editCollectionBook like we should if using videogame style, but I'll refactor later.
    {
        $user = Auth::user();

        // Find specific pivot record for this book and user
        $collectionItem = $user->books()
            ->where('book_id', $book->id)
            ->first();

        // Check if the book is in the user's collection
        if (!$collectionItem || !$collectionItem->pivot) {
            return redirect()->route('my-books')->with('error', 'Book not found in your collection');
        }

        // We pass the Book model ($book) and the pivot data ($collectiomItem->pivot) to the view
        // We also make sure the view exists at resources/views/books/edit-collection-item.blade.php
        return view('books.edit-collection-item', [
            'book' => $book,
            'pivotData' => $collectionItem->pivot,
        ]);
    }

    /**
     * Update the user's collection details for the specified book in storage.
     * Route: my-books.update (PUT/PATCH /my-books/{book})
     * 
     * @param Request $request
     * @param Book $book Route model binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Book $book)
    {
        $user = Auth::user();

        // 1. Vaildate the submitted form data
        $validatedPivotData = $request->validate([
            'status' => ['required', Rule::in(['to-read', 'reading', 'read', 'on-hold', 'abandoned'])],
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // 2. Update the pivot table record ('user_book')
        $updated = $user->books()->updateExistingPivot($book->id, $validatedPivotData);

        if ($updated) {
            return redirect()->route('my-books')->with('success', 'Collection details for' . $book->title . ' updated successfully');
        } else {
            return redirect()->back()->with('error', 'Failed to update collection details for ' . $book->title . '. Reocrd might not exist or data was unchanged.');
        }
    }

    /**
     * Remove the specified book from authenticated user's collection.
     * Route: my-books.destroy (DELETE /my-books/{book})
     * 
     * @param Book $book Route model binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Book $book)
    {
        $user = Auth::user();

        // Use the 'books' relationship on the User model
        // detach() removes the entru from the pivot table ('user_book')

        $detached = $user->books()->detach($book->id);

        if ($detached) {
            return redirect()->route('my-books')
                ->with('success', '"' . $book->title . '" removed from your collection');
        } else {
            return redirect()->route('my-books')
                ->with('warning', '"' . $book->title . '" was not found in your collection.');
        }

    }

    /**
     * Add an existin book (from local DB) to the authenticated user's collection.
     * Triggered by form on the /books page.
     * Route: books.add-local-to-collection (POST /books/{book}/add-to-collection)
     * 
     * @param Request $request
     * @param Book $book Route model binding
     * @return \Illuminate\Http\RedirectResponse
     */

    public function addLocalBookToCollection(Request $request, Book $book)
    {
        $user = Auth::user();

        // Check if the book is already in the user's collection
        if ($user->books()->where('book_id', $book->id)->exists()) {
            return redirect()->back()->with('"' . $book->title . '" is already in your collection.');
        }

        // 2. Validate data from the form on the index page
        $validatedPivotData = $request->validate([
            'status' => ['required', Rule::in(['to-read', 'reading', 'read', 'on-hold', 'abandoned'])],
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // 3. Attach the book using validated pivot data
        try {
            $user->books()->attach($book->id, $validatedPivotData);
            return redirect()->route('my-books')->with('success', '"' . $book->title . '" added to your collection');
        } catch (\Exception $e) {
            Log::error("Error adding local book to collection: " . $e->getMessage(), [
                'user_id' => $user->id,
                'book_id' => $book->id
            ]);
            return redirect()->route('books.index') // Redirect back to book list
                ->with('error', 'Could not add "' . $book->title . '" to your collection due to an error.');
        }
    }
}