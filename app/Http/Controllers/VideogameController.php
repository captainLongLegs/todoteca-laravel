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
        // Fetch all videogames from database
        $videogames = Videogame::latest()->paginate(12);

        // --- DD #1 - Check the videogames data ---
        // dd('Videogames Index - Fetched videogames: ', $videogames);
        // --- END DD #1 ---

        return view('videogames.index', compact('videogames'));
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
            Log::error("RAWG API Connection Error: " . $e->getMessage(), ['query' => $query]);
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
        // --- DD #1 - Check the request data ---
        // dd('Store From Search - Request received: ', $request->all());
        // --- END DD #1 ---

        // 1. Validate Incoming Request Data
        $validatedData = $request->validate([
            'api_id' => 'required|integer',
            'slug' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'background_image' => 'nullable|url',
            'released' => 'nullable|date_format:Y-m-d',
            'platforms_string' => 'nullable|string',
            'genres_string' => 'nullable|string',

            // User input from the form
            'status' => ['required', Rule::in(['whislist', 'backlog', 'playing', 'completed', 'dropped'])],
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:5000',
        ]);

        // --- DD #2 - Check the validated data ---
        // dd('Store From Search - Validation passed. Data: ', $validatedData);
        // --- END DD #2 ---

        $user = Auth::user();

        // 2. Find or Create the Videogame
        try {
            // We use firstOrCreate to find the videogame by its unique API ID
            $videogame = Videogame::firstOrCreate(
                ['api_id' => $validatedData['api_id']], // Search key
                // Data to use if created
                [
                    'slug' => $validatedData['slug'],
                    'name' => $validatedData['name'],
                    'background_image' => $validatedData['background_image'],
                    'released' => $validatedData['released'],
                    // REMOVED 'developers' => $validatedData['developers'],
                    // REMOVED 'publishers' => $validatedData['publishers'],
                ]
            );

            // --- DD #3 - Check if videogame is found or created ---
            //dd('Store From Search - Videogame found or created: ', $videogame->toArray(), 'Exists in DB', $videogame->exists, 'Was recently created', $videogame->wasRecentlyCreated);
            // --- END DD #3 ---

            // 3. Handle Platforms and Genres

            // Platforms
            if (!empty($validatedData['platforms_string'])) {
                $platformNames = explode(',', $validatedData['platforms_string']);
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
            if (!empty($validatedData['genres_string'])) {
                $genreNames = explode(',', $validatedData['genres_string']);
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
            $user->videogames()->attach($videogame->id, $pivotData);

            // 5. Redirect with Success Message
            return redirect()->route('my-videogames')
                ->with('success', 'Videogame added to your collection successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle potential database errors
            Log::error("Database error adding videogame: " . $e->getMessage(), ['api_id' => $validatedData['api_id'] ?? null]);
            return redirect()->back()->with('error', 'Could not add the game due to a database issue. Please try again.');
        } catch (\Exception $e) {
            // --- DD #exception ---
            // dd('Store From Search - Exception during storeFromSearch: ', $e->getMessage(), $e);
            // --- END DD #exception ---

            // Handle any other unexpected errors
            Log::error("Error in storeFromSearch: " . $e->getMessage(), ['api_id' => $validatedData['api_id'] ?? null]);
            report($e); // Optional: report the error to the logging system. FTM I leave it here
            return redirect()->back()->with('error', 'An unexpected error occurred while adding the game.');

        }
    }

    /**
     * Display the user's videogame collection.
     * Route: my-videogames (GET /my-videogames)
     */
    public function myCollection()
    {
        // 1. Ensure user is authenticated (middleware should handle this).
        // 2. Fetch videogames associated with the user from the pivot table.
        $user = Auth::user();
        $videogames = $user->videogames()
            ->withPivot('status', 'rating', 'comment', 'playtime_hours')
            ->orderByPivot('created_at', 'desc')
            ->paginate(12);

        $platforms = Platform::orderBy('name')->get();
        $genres = Genre::orderBy('name')->get();

        // 3. Return a view displaying the collection.
        return view('videogames.my-collection', compact('videogames', 'platforms', 'genres')); 
    }

    public function create()
    {
        // Returns the view that contains the form to add videogames manually
        return view('videogames.create');
    }

    public function store(Request $request)
    {
        // 1. Validate the data from the manual form
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:videogames,name',
            'developer' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'released' => 'nullable|date',
            'background_image' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'age_rating' => 'nullable|string|max:50',
        ]);

        // --- DD #1 - Check the data ---
        // dd('Manual Store - Validation passed. Data: ', $validatedData);
        // --- END DD #1 ---

        // 2. Create the videogame in the database
        try {

            // --- DD #2 - Check if model instance is created ---
            $newVideogame = Videogame::create($validatedData);
            // dd('Manual Store - Videogame created: ', $newVideogame->toArray());
            // --- END DD #2 ---


            // We use mass assignment, base on Videogame model's $fillable property
            $videogame = Videogame::create($validatedData);

            // 3. Redirect after succesful creation
            return redirect()->route('videogames.index')
                ->with('success', $videogame->name . ' has been added to your collection.');

        } catch (\Exception $e) {
            // --- DD #3 - Check for exceptions ---
            // dd('Manual Store - Exception during create: ', $e->getMessage(), $e);
            // --- END DD #3 ---

            Log::error("Error storing mannually added videogame: " . $e->getMessage());
            return redirect()->back()
                ->with('error' . 'Failed to add videogame. Please check the data and try again.')
                ->withInput(); // Redirect back with input data
        }

    }

    /**
     * Add an existing videogame (from local DB) to the authenticated user's collection.
     * Triggered by form on the /vidoegames index page.
     * Route: videogames.add-local-to-collection (POST /videogames/{videogame}/add-to-collection)
     * 
     * @param Request $request
     * @param Videogame $videogame Route model binding
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addLocalToCollection(Request $request, Videogame $videogame)
    {
        $user = Auth::user();

        // 1. Check if the user already has this game in their collection
        if ($user->videogames()->where('videogame_id', $videogame->id)->exists()) {
            return redirect()->route('videogames.index')
                ->with('warning', $videogame->name . ' is already in your collection.')
                ->withFragment('game-' . $videogame->id); // Redirect to the videogame section
        }

        // 2. Validate data coming from the form
        $validatedPivotData = $request->validate([
            'status' => ['required', Rule::in(['whislist', 'backlog', 'playing', 'completed', 'dropped'])],
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:5000',
            'playtime_hours' => 'nullable|integer|min:0|max:10000',
        ]);

        // 3. Attach the videogame (which already exists locally) to the user's collection using the validated pivot data
        try {
            $user->videogames()->attach($videogame->id, $validatedPivotData);

            return redirect()->route('my-videogames')
                ->with('success', $videogame->name . ' has been added to your collection.');
        } catch (\Exception $e) {
            Log::error("Error adding local game to collection: " . $e->getMessage(), [
                'videogame_id' => $videogame->id,
                'user_id' => $user->id,
            ]);
            return redirect()->route('videogames.index')
                ->with('error', 'Failed to add ' . $videogame->name . ' to your collection. Please try again.');
        }
    }

    /**
     * Show the form for editing the user's collection details for a specific videogame.
     * Route: my-videogames.edit (GET /my-videogames/{videogame}/edit)
     * 
     * @param Videogame $videogame Route model binding
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function editCollectionItem(Videogame $videogame)
    {
        $user = Auth::user();

        // First, we find the specific pivot record for this user and videogame
        $collectionItem = $user->videogames()
            ->where('videogame_id', $videogame->id)
            ->first();

        // Then, we check if the game is actually in the user's collection
        if (!$collectionItem || !$collectionItem->pivot) {
            return redirect()
                ->route('my-videogames')
                ->with('error', 'Videogame not found in your collection.');
        }

        // We pass the Videogame model and the pivot data to the view
        return view('videogames.edit-collection-item', [
            'videogame' => $videogame,
            'pivotData' => $collectionItem->pivot,
        ]);
    }

    /**
     * Update the user's collection details for a specific videogame.
     * Route: my-videogames.update (PUT/PATCH /my-videogames/{videogame})
     * 
     * @param Request $request
     * @param Videogame $videogame Route model binding
     * @return \Illuminate\Http\RedirectResponse
     */

    public function updateCollectionItem(Request $request, Videogame $videogame)
    {
        $user = Auth::user();

        // 1. We validate the incoming request data
        $validatedPivotData = $request->validate([
            'status' => ['required', Rule::in(['whislist', 'backlog', 'playing', 'completed', 'dropped'])],
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:5000',
            'playtime_hours' => 'nullable|integer|min:0|max:10000',
        ]);

        // 2. We update the pivot table record
        $updated = $user->videogames()->updateExistingPivot($videogame->id, $validatedPivotData);

        if ($updated) {
            return redirect()
                ->route('my-videogames')
                ->with('success', 'Collection details for ' . $videogame->name . ' updated successfully.');
        } else {
            return redirect()
                ->route('my-videogames')
                ->with('warning', 'No changes were made to the collection details for ' . $videogame->name . '. Record might not exist or data was unchanged.');
        }
    }

    /**
     * Remove a videogame from the user's collection.
     * Route: my-videogames.destroy (DELETE /my-videogames/{videogame})
     */
    public function removeFromCollection(Videogame $videogame)
    {
        $user = Auth::user();

        // We use the relationshop ('videogames') defined on the User model
        // detach() removes the entry from the pivot talbe
        $detached = $user->videogames()->detach($videogame->id);

        if ($detached) {
            // If derach returns > 0, it means a record was removed 
            return redirect()->route('my-videogames')
                ->with('success', $videogame->name . ' has been removed from your collection.');
        } else {
            // If detach returns 0, it means the videogame was not in the user's collection
            return redirect()->route('my-videogames')
                ->with('info', $videogame->name . ' was not found in your collection.');
        }
    }

}