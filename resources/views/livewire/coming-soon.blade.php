<div wire:init="loadComingSoon" class="coming-soon-container space-y-10 mt-8">
    @forelse($comingSoon as $game)
        <div class="game flex">
            <a href="#">
                <img src="{{ Str::replaceFirst('thumb', 'cover_small', $game['cover']['url']) }}" alt="game cover" class="w-16 hover:opacity-75 transition ease-in-out duration-150">
            </a>
            <div class="ml-4">
                <a href="#" class="hover:text-gray-300">{{ $game['name'] }}</a>
                <div class="text-gray-400 text-sm mt-1">{{ date("M j Y", $game['first_release_date']) }}</div>
            </div>
        </div>
    @empty
        @foreach(range(1, 4) as $game)
            <div class="game flex">
                <div class="bg-gray-800 w-16 h-20 flex-none"></div>
                <div class="ml-4">
                    <div class="text-transparent bg-gray-700 rounded leading-tight">Title goes here now.</div>
                    <div class="text-transparent bg-gray-700 rounded inline-block leading-tight text-sm mt-2">Sept 14 2022</div>
                </div>
            </div>
        @endforeach
    @endforelse
</div>
