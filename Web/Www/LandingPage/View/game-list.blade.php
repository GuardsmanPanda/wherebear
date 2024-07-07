<?php declare(strict_types=1); ?>

@if(count($games) === 0)
    There are currently no games.
@else
    <div class="grid gap-2">
        @foreach($games as $game)
            @if($game->is_in_game)
                @if(!$loop->first)
                    <hr class="border-gray-600">
                @endif
                <a class="hover:text-white hover:scale-102 duration-75 transition-transform"
                   href="/game/{{$game->id}}/lobby">
                    <h3 class="text-lg font-medium">{{$game->user_display_name}}'s Game <span class="text-yellow-400">(Playing)</span>
                    </h3>
                    <p class="pl-1 text-sm">{{$game->number_of_rounds}} rounds, {{$game->round_duration_seconds}}
                        seconds
                        each.</p>
                </a>
            @endif
        @endforeach
        @foreach($games as $game)
            @if(!$game->is_in_game)
                @if(!$loop->first)
                    <hr class="border-gray-600">
                @endif
                <a class="hover:text-white hover:scale-102 duration-75 transition-transform"
                   href="/game/{{$game->id}}/lobby">
                    <h3 class="text-lg font-medium">{{$game->user_display_name}}'s Game</h3>
                    <p class="pl-1 text-sm">{{$game->number_of_rounds}} rounds, {{$game->round_duration_seconds}}
                        seconds
                        each.</p>
                </a>
            @endif
        @endforeach
    </div>
@endif
