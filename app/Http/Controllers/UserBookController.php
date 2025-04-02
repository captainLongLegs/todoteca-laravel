<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class UserBookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'status' => 'required|in:to-read, reading, read',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable',
        ]);

        $book = Book::firstOrCreate(
            ['isbn' => $validatedData['isbn']], //Search by ISBN if available
            [
                'title' => $validatedData['title'],
                'author' => $validatedData['author'],
            ]
        );

        // Check if the book already exists in the user's collection
        if (auth()->user()->books()->where('book_id', $book->id)->exists()) {
            return redirect()->back()->with('error', 'This book is already in your collection');
        }
        
        // Attach the book to the user with metadata
        auth()->user()->books()->attach($book->id, $validatedData);

        // Redirect to the useer's collection
        return redirect()
            ->route('my-collection')
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
