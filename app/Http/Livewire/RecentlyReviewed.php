<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class RecentlyReviewed extends Component
{
    public array $recentlyReviewed = [];

    public function loadRecentlyReviewed(){
        $before = Carbon::now()->subMonths(2)->timestamp;
        $current = Carbon::now()->timestamp;

        $recentlyReviewedUnformatted = Cache::remember('recently-reviewed', 60, function() use($before, $current) {
            return Http::withHeaders(config('services.igdb'))
                ->withBody('
                    fields name, cover.url, first_release_date, platforms.abbreviation, rating, rating_count, summary;
                    where platforms = (6, 130, 167, 169)
                    & (first_release_date >= '. $before .' & first_release_date < '. $current .'
                    & rating_count > 5);
                    sort rating desc;
                    limit 3;',
                    'text/plain')
                ->post('https://api.igdb.com/v4/games')->json();
        });

        $this->recentlyReviewed = $this->formatForView($recentlyReviewedUnformatted);
    }

    public function render()
    {
        return view('livewire.recently-reviewed');
    }

    private function formatForView($games){
        return collect($games)->map(function ($game) {
            return collect($game)->merge([
                'first_release_date' => date("M j Y", $game['first_release_date']),
                'coverImageUrl' => Str::replaceFirst('thumb', 'cover_big', $game['cover']['url']),
                'rating' => isset($game['rating']) ? round($game['rating']).'%' : null,
                'platforms' => collect($game['platforms'])->pluck('abbreviation')->implode(', '),
            ]);
        })->toArray();
    }
}
