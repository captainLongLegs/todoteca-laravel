<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Importing Laravel's HTTP Client facade
use Illuminate\Support\Facades\Log; // Importing Log facade for error logging
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'comment' => 'nullable|string|max:1000',
            'format' => 'nullable|max:255',
            'tags' => 'nullable',
        ]);

        Book::create($validatedData);

        return redirect()->route('books.index')->with('success', 'Book added successfully.');
    }

    /**
     *  Search for data using the API
     */

    public function search()
    {
        return view('books.search');
    }

    public function searchResults(Request $request)
    {
        $query = $request->input('query');
        $client = new Client();
        $response = $client->get('https://openlibrary.org/search.json', [
            'query' => [
                'q' => $query,
                'fields' => 'title,author_name,isbn,first_publish_year',
            ],
        ]);
        $data = json_decode($response->getBody(), true);
        $books = $data['docs'] ?? [];

        return view('books.search-results', compact('books'));
    }

    /**
     * Store a book from the search results into the database
     */

    public function storeFromSearch(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:20',
            'status' => 'required|in:to-read,reading,read',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Find or create the book

        try {

            $book = Book::firstOrCreate(
                ['isbn' => $validated['isbn']],
                [
                    'title' => $validated['title'],
                    'author' => $validated['author'],
                ]
            );

            // Handling missing fields, checking for error in 'comment' being empty?

            $pivotData = [
                'status' => $validated['status'],
                'rating' => $validated['rating'] ?? null,
                'comment' => $validated['comment'] ?? null,
            ];

            // Attach the book to the authenticated user with additional data
            $user->books()->attach($book->id, $pivotData);
            return redirect()->route('my-books')->with('success', 'Book added to your collection.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle potential database errors
            Log::error("Database error adding book: " . $e->getMessage(), ['isbn' => $validatedData['isbn'] ?? null]);
            return redirect()->back()->with('error', 'Could not add the book due to a database issue. Please try again.');
        } catch (\Exception $e) {
            // Handle any other unexpected errors
            Log::error("Error in storeFromSearch: " . $e->getMessage(), ['isbn' => $validatedData['isbn'] ?? null]);
            report($e); // Optional: report the error to the logging system. FTM I leave it here
            return redirect()->back()->with('error', 'An unexpected error occurred while adding the book.');

        }
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
