<?php declare(strict_types=1); ?>

@if(count($games) === 0)
    There are currently no games.
@else
    <div class="grid gap-2">
        @foreach($games as $game)
            @if(!$loop->first)
                <hr class="border-gray-600">
            @endif
            <a class="hover:text-white hover:scale-102 duration-75 transition-transform"
               href="/game/{{$game->id}}/lobby">
                <h3 class="text-lg font-medium">{{$game->display_name}}'s Game
                    @if($game->is_in_game)
                        <span class="text-yellow-400">(Playing)</span>
                    @endif
                </h3>
                <p class="pl-1 text-sm">{{$game->number_of_rounds}} rounds, {{$game->round_duration_seconds}}
                    seconds
                    each.</p>
            </a>
        @endforeach
    </div>
@endif
