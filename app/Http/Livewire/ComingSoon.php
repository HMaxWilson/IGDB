<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ComingSoon extends Component
{
    public array $comingSoon = [];

    public function loadComingSoon()
    {
        $after = Carbon::now()->addMonths(2)->timestamp;
        $current = Carbon::now()->timestamp;

        $this->comingSoon = Cache::remember('coming-soon', 60, function() use($current, $after) {
            return Http::withHeaders(config('services.igdb'))
                ->withBody('
                    fields name, cover.url, first_release_date, platforms.abbreviation, rating, rating_count;
                    where platforms = (6, 130, 167, 169)
                    & (first_release_date >= '. $current .' & first_release_date < '. $after .');
                    sort first_release_date asc;
                    limit 4;',
                    'text/plain')
                ->post('https://api.igdb.com/v4/games')->json();
        });
    }

    public function render()
    {
        return view('livewire.coming-soon');
    }
}
