<?php declare(strict_types=1); ?>

@if(count($games) === 0)
    There are currently no games.
@else
    <div class="grid gap-2 divide-y divide-gray-600">
        @foreach($games as $game)
            <a class="@if(!$loop->first) pt-2 @endif hover:text-white" href="/game/{{$game->id}}/lobby">
                <h3 class="text-lg font-bold">{{$game->user_display_name}}'s Game</h3>
                <p class="pl-1 text-sm">{{$game->number_of_rounds}} rounds, {{$game->round_duration_seconds}} seconds
                    each.</p>
            </a>
        @endforeach
    </div>
@endif
