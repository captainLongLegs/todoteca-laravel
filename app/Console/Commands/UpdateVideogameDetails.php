<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Videogame;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class UpdateVideogameDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'videogames:update-details 
                            {--limit=50 : The number of games to process per run} 
                            {--id= : Update a specific game by its local database ID}
                            {--force-update : Force update even if developer field is already populated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches and updates details (like developer) for videogames from the RAWG API.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting to update videogame details from RAWG.io...');

        $limit = (int) $this->option('limit');
        $specificId = $this->option('id');
        $forceUpdate = $this->option('force-update');

        $query = Videogame::query();

        if ($specificId) {
            $query->where('id', $specificId);
        } else {
            if (!$forceUpdate) {
                // By default, only update games where developer is null (or empty)
                $query->where(function ($q) {
                    $q->whereNull('developer')->orWhere('developer', '');
                });
            }
            $query->whereNotNull('api_id')->orderBy('id')->take($limit);
        }

        $videogamesToUpdate = $query->get();

        if ($videogamesToUpdate->isEmpty()) {
            if ($specificId) {
                $this->warn("Videogame with local ID {$specificId} not found, or does not meet update criteria (e.g., developer already set and --force-update not used).");
            } else {
                $this->info('No videogames found requiring an update based on current criteria.');
            }
            return Command::SUCCESS;
        }

        $apiKey = Config::get('services.rawg.key');
        $baseUrl = Config::get('services.rawg.base_url');

        if (empty($apiKey) || empty($baseUrl)) {
            $this->error('RAWG API Key or Base URL not configured in config/services.php or .env file.');
            return Command::FAILURE;
        }

        $updatedCount = 0;
        $failedCount = 0;
        $progressBar = $this->output->createProgressBar($videogamesToUpdate->count());
        $progressBar->start();

        foreach ($videogamesToUpdate as $game) {
            $this->line(''); 
            $this->comment("Processing: {$game->name} (Local ID: {$game->id}, API ID: {$game->api_id})");

            if (empty($game->api_id)) {
                $this->warn("  Skipping {$game->name} - missing api_id.");
                $progressBar->advance();
                continue;
            }

            try {
                $response = Http::timeout(20) 
                    ->get("{$baseUrl}/games/{$game->api_id}", ['key' => $apiKey]);

                if ($response->successful()) {
                    $detailsData = $response->json();
                    $updateData = [];

                    // --- Extract Developer ---
                    $developerName = null;
                    if (!empty($detailsData['developers']) && is_array($detailsData['developers']) && count($detailsData['developers']) > 0) {
                        $developerName = $detailsData['developers'][0]['name'] ?? null;
                    }
                    if ($developerName && ($game->developer !== $developerName || $forceUpdate)) {
                        $updateData['developer'] = $developerName;
                    }

                    // --- Extract Publisher (Example) ---
                    // $publisherName = null;
                    // if (!empty($detailsData['publishers']) && is_array($detailsData['publishers']) && count($detailsData['publishers']) > 0) {
                    //     $publisherName = $detailsData['publishers'][0]['name'] ?? null;
                    // }
                    // if ($publisherName && (optional($game)->publisher !== $publisherName || $forceUpdate)) { // 'publisher' column must exist
                    //     $updateData['publisher'] = $publisherName;
                    // }

                    // --- Extract Description (Example) ---
                    // $description = $detailsData['description_raw'] ?? $detailsData['description'] ?? null;
                    // if ($description && ($game->description !== $description || $forceUpdate)) { // 'description' column must exist
                    //     $updateData['description'] = $description;
                    // }

                    // --- Update other fields if desired, e.g., name, released, background_image ---
                    // if (isset($detailsData['name']) && ($game->name !== $detailsData['name'] || $forceUpdate)) {
                    //     $updateData['name'] = $detailsData['name'];
                    // }
                    // if (isset($detailsData['released']) && ($game->released ? $game->released->toDateString() : null) !== $detailsData['released'] || $forceUpdate) {
                    //    $updateData['released'] = $detailsData['released'];
                    // }


                    if (!empty($updateData)) {
                        $game->update($updateData);
                        $this->info("  Updated: " . implode(', ', array_keys($updateData)));
                        $updatedCount++;
                    } else {
                        $this->line("  No new details to update for {$game->name}.");
                        if (is_null($game->developer) && is_null($developerName) && !$forceUpdate) {
                            $this->warn("  Developer info still missing for {$game->name} after API call (API might not provide it).");
                        }
                    }
                } else {
                    $this->error("  Failed to fetch API details for {$game->name} (API ID: {$game->api_id}). Status: " . $response->status());
                    Log::warning("Artisan UpdateVideogameDetails: Failed RAWG API for game API ID {$game->api_id}", ['status' => $response->status()]);
                    $failedCount++;
                }
            } catch (\Exception $e) {
                $this->error("  Exception while processing {$game->name}: " . $e->getMessage());
                Log::error("Artisan UpdateVideogameDetails: Exception for game API ID {$game->api_id}", ['message' => $e->getMessage()]);
                $failedCount++;
            }
            $progressBar->advance();
            sleep(1);
        }

        $progressBar->finish();
        $this->line(''); 
        $this->info("Update process finished. {$updatedCount} videogames updated. {$failedCount} failed.");
        return Command::SUCCESS;
    }
}