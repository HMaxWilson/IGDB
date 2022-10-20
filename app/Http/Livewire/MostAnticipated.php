<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class MostAnticipated extends Component
{
    public array $mostAnticipated = [];

    public function loadMostAnticipated()
    {
        $after = Carbon::now()->addMonths(4)->timestamp;
        $current = Carbon::now()->timestamp;

        $mostAnticipatedUnformatted = Cache::remember('most-anticipated', 60, function() use($current, $after) {
            return Http::withHeaders(config('services.igdb'))
                ->withBody('
                    fields name, cover.url, first_release_date, platforms.abbreviation, rating, rating_count;
                    where platforms = (6, 130, 167, 169)
                    & (first_release_date >= '. $current .' & first_release_date < '. $after .');
                    limit 4;',
                    'text/plain')
                ->post('https://api.igdb.com/v4/games')->json();
        });

        $this->mostAnticipated = $this->formatForView($mostAnticipatedUnformatted);
    }

    public function render()
    {
        return view('livewire.most-anticipated');
    }

    private function formatForView($games){
        return collect($games)->map(function ($game) {
            return collect($game)->merge([
                'first_release_date' => date("M j Y", $game['first_release_date']),
                'coverImageUrl' => Str::replaceFirst('thumb', 'cover_small', $game['cover']['url'])
            ]);
        })->toArray();
    }
}
