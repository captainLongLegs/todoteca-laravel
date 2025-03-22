<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::all(); // Fetch all books from the database
        return view('books.index', compact('books')); // Pass the books to the view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
            'isbn' => 'required|unique:books|max:255',
            'genre' => 'nullable|max:255',
            'cover_image' => 'nullable|url',
            'description' => 'nullable',
            'publisher' => 'nullable|max:255',
            'publication_year_this_publisher' => 'nullable|integer',
            'publication_year_original' => 'nullable|integer',
            'valoration' => 'nullable|integer|min:1|max:5',
            'comments' => 'nullable',
            'format' => 'nullable|max:255',
            'tags' => 'nullable',
        ]);

        Book::create($validatedData); 

        return redirect()->route('books.index')->with('success', 'Book added successfully.');
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
