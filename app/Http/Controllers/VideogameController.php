<?php

namespace App\Http\Controllers;

use App\Models\Videogame;
use App\Models\Platform;
use App\Models\Genre;
use Illuminate\Http\Request;
use GuzzleHttp\Client; // Not using it atm
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Importing Laravel's HTTP Client facade
use Illuminate\Support\Facades\Log; // Importing Log facade for error logging
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VideogameController extends Controller
{
    /**
     * Display a listing of the videogames saved locally
     */

    public function index()
    {
        // Placeholder: Fetch all videogames from the database
        // $videogames = \App\Models\Videogame::all();
        // return view('videogames.index', compact('videogames'));

        return view('videogames.index');
    }

    /**
     * Show the form for searching videogames via API.
     * Route: videgoames.search (GET /videogames/search)
     */

    public function search()
    {
        return view('videogames.search');
    }

    /**
     * Fetch and display searh resutls from the API.
     * Route: videogames.search.results (GET /videogames/search/results)
     */

     public function searchResults(Request $request)
     {
        $query = $request->input('query'); 
        $videogames = [];
        $error = null;

        // Ensure we have a query to search for
        if (empty($query)) {
            return redirect()->route('videogames.search')->with('error', 'Please enter a search term.');
        }

        // Get the API key and base URL from .env file
        $apiKey = config('services.rawg.key');
        $baseUrl = config('services.rawg.base_url');

        // --- API Call using Laravel HTTP Client ---
        try {
            // First, we make sure the API key and URL are configured
            if (empty($apiKey) || empty($baseUrl)) {
                throw new \Exception('RAWG API key or base URL not configured in config/services .php or .env file.');
            }
        

            $response = HTTP::timeout(10) // We set a timeout of 10 seconds
                ->get("{$baseUrl}/games", [
                'key' => $apiKey,
                'search' => $query,
                'page_size' => 15, // Limiting the results to 15. Arbitrary atm
                ]);


            // _____-----_____-----_____-----_____ 
            // TROUBLESHOOTING -> There are no results when searching for any game
            // dd($response->json()); // Uncomment this line to see the raw response
            // Check for succesful response (code after dd() won't run yet)
            // _____-----_____-----_____-----_____

            // Check for succesful response
            if ($response->successful()) { // Status code 2XX
                $data = $response->json(); // We decode JSON response body
                $videogames = $data['results'] ?? []; // Adjust based on the API's response structure
            } else {
                // Handling error response
                $error = "Error fetching data from RAWG API: " . $response->status();
                Log::error("RAWG API Error: Status {$response->status()}", ['query' => $query, 'response_body' => $response->body()]);
                $videogames = [];
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Handle connection errors (timeout, DNS issues, etc.)
            $error = "could not connext to the Videogame API. Please try again later.";
            Log::error("RAWG API Connection Error: " . $e -> getMessage(), ['query' => $query]);
        } catch (\Exception $e) {
            // handle other errors (e.g., missing config, unexpected issues)
            $error = "An unexpected error occurred while searching for videogames.";
            Log::error("Videogames Search Error: " . $e->getMessage(), ['query' => $query]);
            report($e); // Optional: report the error to the logging system
        }

        // _____-----_____-----_____-----_____
        // Troubleshooting
        // dd($videogames, $error); // Uncomment this line to see the results
        // Check the structure of $videogames and $error
        // _____-----_____-----_____-----_____

        // Return the results view, passing the games data, query and any error message
        return view('videogames.search-results', compact('videogames', 'query', 'error'));
    }
      

    /**
     * Store a videogame found via API search into the local database
     * and add it to the user's collection.
     * Route: videogames.store-from-search (POST /videogames/store-from-search)
     */
    public function storeFromSearch(Request $request)
    {
        // 1. Validate Incoming Request Data
        $validatedData = $request->validate([
            'api_id' => 'required|integer',
            'slug' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'background_image' => 'nullable|url',
            'released' => 'nullable|date_format:Y-m-d',
            'developer' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'platforms_string' => 'nullable|string',
            'genres_string' => 'nullable|string',

            // User input from the form
            'status' => ['required', Rule::in(['whislist', 'backlog', 'playing', 'completed', 'dropped'])],
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:5000',
        ]);

        $user = Auth::user();

        // 2. Find or Create the Videogame
        try {
            // We use firstOrCreate to find the videogame by its unique API ID
            $videogame = Videogame::firstOrCreate(
                ['api_id' => $validatedData['api_id']], // Search key
                // Data to use if created
                [ 'slug' => $validatedData['slug'],
                  'name' => $validatedData['name'],
                  'background_image' => $validatedData['background_image'],
                  'released' => $validatedData['released'],
                  'developer' => $validatedData['developer'],
                  'publisher' => $validatedData['publisher'],
                ]
            );

            // 3. Handle Platforms and Genres

            // Platforms
            if (!empty($validatedData['platforms_string'])) {
                $platformNames = explode (',', $validatedData['platforms_string']);
                $platformIds = [];
                foreach ($platformNames as $platformName) {
                    // Find or create the platform
                    $platform = Platform::firstOrCreate(
                        ['name' => trim($platformName)], // Search key
                        ['slug' => Str::slug(trim($platformName))] // Create key
                    );
                    $platformIds[] = $platform->id; // Collect platform IDs
                }
                // We sync the platforms for this game
                $videogame->platforms()->sync($platformIds);
            }

            // Genres
            if (!emptu($validatedData['genres_string'])) {
                $genreNames = explode (',', $validatedData['genres_string']);
                $genreIds = [];
                foreach ($genreNames as $genreName) {
                    // Find or create the genre
                    $genre = Genre::firstOrCreate(
                        ['name' => trim($genreName)], // Search key
                        ['slug' => Str::slug(trim($genreName))] // Create key
                    );
                    $genreIds[] = $genre->id; // Collect genre IDs
                }
                // We sync the genres for this game
                $videogame->genres()->sync($genreIds);
            }

            // 4. Attach the videogame to the user's collection
            // First, we check if the videogame is already in the user's collection.
            if ($user->videogames()->where('videogame_id', $videogame->id)->exists()) {
                return redirect()->route('videogames.search.results', ['query' => $request->input('query')])
                    ->with('info', 'Videogame already in your collection.');
            }

            // We prepare pivot data
            $pivotData = [
                'status' => $validatedData['status'],
                'rating' => $validatedData['rating'] ?? null,
                'comment' => $validatedData['comment'] ?? null,
            ];

            // Attach the videogame to the user's collection with pivot data
            $user->videogames89->attach($videogame->id, $pivotData);

            // 5. Redirect with Success Message
            return redirect()->route('my-videogames')
                ->with('success', 'Videogame added to your collection successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle potential database errors
            Log::error("Database error adding videogame: " . $e->getMessage(), ['api_id' => $validatedData['api_id'] ?? null]);
            return redirect()->back()->with('error', 'Could not add the game due to a database issue. Please try again.');
        } catch (\Exception $e) {
            // Handle any other unexpected errors
            Log::error("Error in storeFromSearch: " . $e->getMessage(), ['api_id' => $validatedData['api_id'] ?? null]);
            report($e); // Optional: report the error to the logging system. FTM I leave it here
            return redirect()->back()->with('error', 'An unexpected error occurred while adding the game.');

        }

        // --- Placeholder ---
        // This will be similar to BookController::storeFromSearch
        // 1. Validate incoming request data (hidden fields + user status/rating/comment)
        // 2. Need a Videogame model and migration.
        // 3. Use Videogame::firstOrCreate(...) based on a unique identifier from the API (like a slug or API ID).
        // 4. Need User <-> Videogame relationship (e.g., user_videogame pivot table).
        // 5. Attach the videogame to Auth::user()->videogames() with pivot data.
        // 6. Redirect to 'my-videogames' with a success message.

        return redirect()->route('my-videogames')->with('info', 'Videogame storing not implemented yet.'); // Temporary response
    }

    /**
     * Display the user's videogame collection.
     * Route: my-videogames (GET /my-videogames)
     */
    public function myCollection()
    {
        // --- Placeholder ---
        // 1. Ensure user is authenticated (middleware should handle this).
        // 2. Fetch videogames associated with the user from the pivot table.
        //    $user = Auth::user();
        //    $videogames = $user->videogames()->withPivot('status', 'rating', 'comment')->get(); // Assumes 'videogames' relationship exists on User model
        // 3. Return a view displaying the collection.

         $videogames = []; // Placeholder
         return view('videogames.my-collection', compact('videogames')); // Need to create this view
    }

    // Add other methods like show, create, store (for manual add), edit, update, destroy later if needed
}