<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Book $book)
    {
        // Validate the data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:20',
            'status' => 'required|in:to-read,reading,read',
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
