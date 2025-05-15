<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Importing Laravel's HTTP Client facade
use Illuminate\Support\Facades\Log; // Importing Log facade for error logging
use Illuminate\Support\Facades\Schema; // Importing Schema facade to validate column names dynamically
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    /**
     * Display a listing of local books.
     * Handles sorting and filtering of books.
     */
    public function index(Request $request)
    {
        // Columns allowed for sorting.
        $allowedSortColumns = ['title', 'author', 'created_at', 'updated_at']; // For now...

        $sortBy = $request->query('sort_by', default: 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');

        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }
        if (!in_array(strtolower($sortDir), ['asc', 'desc'])) {
            $sortDir = 'desc';
        }

        // Alternative validation (more dynamic but slightly more complex):
        // $columns = Schema::getColumnListing('books'); 
        // if (!in_array($sortBy, $columns)) { $sortBy = 'created_at'; }
        // CAN TRY TO USE THIS FOR FUTURE VALIDATIONS

        // Search query
        $booksQuery = Book::query();

        $searchTerm = $request->query('search');

        if ($searchTerm) {
            $booksQuery->where(function ($query) use ($searchTerm) {
                $query->where('title', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('author', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('isbn', 'LIKE', "%{$searchTerm}%");

            });

        }

        $booksQuery->orderBy($sortBy, $sortDir);

        $books = $booksQuery->paginate(10)->withQueryString();

        return view('books.index', compact('books', 'sortBy', 'sortDir'));
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
        $client = new \GuzzleHttp\Client(['verify' => false]); // Disable SSL verification for Guzzle
        $books = [];
        $error = null;

        try {

            $response = $client->get('https://openlibrary.org/search.json', [
                'query' => [
                    'q' => $query,
                    'fields' => 'key,title,author_name,isbn,first_publish_year,cover_i',
                    'limit' => 20,
                ],
                'timeout' => 10,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $books = $data['docs'] ?? [];

        } catch (\GuzzleHttp\Exception\RequestException $e) {

            Log::error("OpenLibrary API Error: " . $e->getMessage());
            if ($e->hasResponse()) {
                Log::error("OpenLibrary Response: " . $e->getResponse()->getBody());
            }
            $error = "Could not connect to the book search API. Please try again later.";
        } catch (\Exception $e) {
            Log::error("Book search error: " . $e->getMessage());
            $error = "An unexpected error occurred during the book search. Please try again later.";
        }
        return view('books.search-results', compact('books', 'query', 'error'));
    }

    /**
     * Store a book from the search results into the database
     */

    public function storeFromSearch(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:20',
            'cover_image' => 'nullable|url|max:255',
            'status' => ['required', Rule::in(['to-read', 'reading', 'read', 'on-hold', 'abandoned'])],
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        try {

            $book = Book::firstOrCreate(
                ['isbn' => $validated['isbn']],
                [
                    'title' => $validated['title'],
                    'author' => $validated['author'],
                    'cover_image' => $validated['cover_image'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'publisher' => $validated['publisher'] ?? null,
                    'publication_year_this_publisher' => $validated['publication_year_this_publisher'] ?? null,
                    'publication_year_original' => $validated['publication_year_original'] ?? null,
                    'valoration' => $validated['valoration'] ?? null,
                ]
            );

            // --- DD for Cover_Image debugging
            // dd('StoreFromSearch - Book found/created: ', $book->toArray());
            // --- END DD

            if ($user->books()->where('book_id', $book->id)->exists()) {
                return redirect()->back()->with('warning', $book->title . ' is already in your collection.');
            }

            $pivotData = [
                'status' => $validated['status'],
                'rating' => $validated['rating'] ?? null,
                'comment' => $validated['comment'] ?? null,
            ];

            $user->books()->attach($book->id, $pivotData);
            return redirect()->route('my-books')->with('success', 'Book added to your collection.');

        } catch (\Illuminate\Database\QueryException $e) {

            Log::error("Database error adding book: " . $e->getMessage(), ['isbn' => $validatedData['isbn'] ?? null]);
            return redirect()->back()->with('error', 'Could not add the book due to a database issue. Please try again.');

        } catch (\Exception $e) {

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
