<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Videogame;
use GuzzleHttp\Client; // Not using it atm
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Importing Laravel's HTTP Client facade
use Illuminate\Support\Facades\Log; // Importing Log facade for error logging

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
        $apiKey = config('services.rawg.key'); // To-do later -> configure config/services.php
        $baseUrl = config('services.rawg.base_url'); // Samey

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

            // Check for succesful response
            if ($response->successful()) { // Status code 2XX
                $data = $response->json(); // We decode JSON response body
                $videogames = $data['results'] ?? []; // Adjust based on the API's response structure
            } else {
                // Handling error response
                $error = "Error fetching data from RAWG API: " . $response->status();
                Log::error("RAWG API Error: Status {$response->status()}", ['query' => $query, 'response_body' => $response->body()]);
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