<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GameController extends Controller
{
    public function search()
    {
        /**
         * Let's try to get the IGDB API access using this controller 
         * 
         */
        $clientId = env('IGDB_CLIENT_ID');
        $clientSecret = env('IGDB_CLIENT_SECRET');
        $url = 'https://id.twitch.tv/oauth2/token';

        // Step 1: Get Twitch Auth Token
        $token = Http::post($url, [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
        ]);
        $accessToken = $token->json()['access_token'];

        // Step 2: Use the access token to make requests to IGDB
        $igdbUrl = 'https://api.igdb.com/v4/games';

        $games = Http::withHeaders([
            'Client-ID' => $clientId,
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post($igdbUrl, [
            'fields' => 'name, cover.url',
            'limit' => 10,
            'offset' => 0,
            'order' => 'popularity:desc',
        ]);
        $games = $games->json();

        // Step 3: Return the games to the view
        return view ('games', ['games' => $games]);
    }
}
