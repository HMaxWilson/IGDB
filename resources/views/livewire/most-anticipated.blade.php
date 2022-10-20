<div wire:init="loadMostAnticipated" class="most-anticipated-container space-y-10 mt-8">
    @forelse($mostAnticipated as $game)
        <x-game-card-small :game="$game" ></x-game-card-small>
    @empty
        @foreach(range(1, 4) as $game)
            <x-game-card-small-skeleton></x-game-card-small-skeleton>
        @endforeach
    @endforelse
</div>
