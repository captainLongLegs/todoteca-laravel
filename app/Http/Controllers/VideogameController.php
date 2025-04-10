<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Videogame;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

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
        $query = $request->input('query'); // Or other search parameters specific to the game API

        // --- API Logic Placeholder ---
        // You'll need to:
        // 1. Choose a videogame API (e.g., RAWG.io, IGDB via Twitch API, GiantBomb)
        // 2. Get an API key if required.
        // 3. Install GuzzleHttp if you haven't: composer require guzzlehttp/guzzle
        // 4. Implement the API call using GuzzleHttp\Client similar to your BookController
        // 5. Parse the JSON response correctly for videogames.

        $videogames = []; // Placeholder for API results
        $error = null; // Placeholder for error message

        /* Example structure using Guzzle (replace with your actual API details)
        try {
            $client = new \GuzzleHttp\Client();
            $apiKey = env('VIDEOGAME_API_KEY'); // Store keys in .env!
            $response = $client->get('YOUR_VIDEOGAME_API_ENDPOINT', [
                'query' => [
                    'search' => $query,
                    'key' => $apiKey,
                    // other required parameters like fields, page size etc.
                ],
                 'headers' => [ // Some APIs need headers
                    'Accept' => 'application/json',
                    // 'Client-ID' => 'Your-Client-ID', // Example for Twitch/IGDB
                    // 'Authorization' => 'Bearer Your-Access-Token', // Example for Twitch/IGDB
                 ]
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            // --- IMPORTANT: Adapt this part based on the API's response structure ---
            $videogames = $data['results'] ?? []; // Adjust keys ('results', 'games', etc.)

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error("Videogame API Error: " . $e->getMessage());
            $error = "Could not retrieve videogame data. Please try again later.";
            // Optional: report($e);
        }
        */

        // Return the results view, passing the games data or error message
        return view('videogames.search-results', compact('videogames', 'query', 'error')); // Need to create this view
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