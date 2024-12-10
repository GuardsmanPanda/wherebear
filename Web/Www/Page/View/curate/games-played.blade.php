<?php declare(strict_types=1); ?>
@foreach($games as $game)
  <div class="mt-12 border-b border-gray-600">
    <h2 class="text-xl">{{$game->name}}</h2>
  </div>
  <div class="m-2 w-full" hx-get="/page/curate/games-played/game/{{$game->id}}" hx-trigger="load" hx-target="this"></div>
@endforeach