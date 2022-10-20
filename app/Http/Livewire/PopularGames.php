<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class PopularGames extends Component
{
    public array $popularGames = [];

    public function loadPopularGames()
    {
        $before= Carbon::now()->subMonths(2)->timestamp;
        $after = Carbon::now()->addMonths(2)->timestamp;

        $popularGamesUnformatted = Cache::remember('popular-games', 60, function() use($before, $after) {
            return Http::withHeaders(config('services.igdb'))
                ->withBody('
                    fields name, cover.url, first_release_date, platforms.abbreviation, rating, total_rating_count, slug;
                    where platforms = (6, 130, 167, 169)
                    & (first_release_date >= ' . $before . ' & first_release_date <= ' . $after . ')
                    & total_rating_count > 5;
                    sort total_rating_count desc;
                    limit 12;',
                    'text/plain')
                ->post('https://api.igdb.com/v4/games')->json();
        });

        $this->popularGames = $this->formatForView($popularGamesUnformatted);
    }

    public function render()
    {
        return view('livewire.popular-games');
    }

    private function formatForView($games){
        return collect($games)->map(function ($game) {
            return collect($game)->merge([
                'coverImageUrl' => Str::replaceFirst('thumb', 'cover_big', $game['cover']['url']),
                'rating' => isset($game['rating']) ? round($game['rating']).'%' : null,
                'platforms' => collect($game['platforms'])->pluck('abbreviation')->implode(', '),
            ]);
        })->toArray();
    }
}
