<?php declare(strict_types=1); ?>
@php use Illuminate\Support\Facades\DB;use Web\Www\Game\Render\GameRoundResultRender; @endphp
<div class="flex px-4 bg-gray-900 min-h-screen">
  <div class="w-80">
    @foreach($rounds as $round)
      @if(!$loop->first)
        <hr class="border-gray-700">
      @endif
      <div class="pb-3 pt-1 transform hover:scale-105 hover:cursor-pointer duration-50"
           hx-get="/game/{{$game->id}}/result/round/{{$round->round_number}}" hx-target="#round-details">
        <div class="text-center font-bold text-xl truncate">{{$round->country_name}}</div>
        <div class="flex">
          <img src="/static/flag/wavy/{{strtolower($round->cca2)}}.png" width="80"
               alt="Wavy flag">
          <div class="pl-2 flex flex-col justify-between leading-4">
            @foreach(DB::select(<<<SQL
                SELECT u.display_name, u.id FROM game_round_user gru
                LEFT JOIN bear_user u ON u.id = gru.user_id
                WHERE gru.game_id = ? AND gru.round_number = ? ORDER BY gru.points DESC LIMIT 3
            SQL, [$game->id, $round->round_number]) as $ru)
              <div class="flex gap-1">
                <img width="20" alt="rank icon"
                     src="/static/img/icon/{{$loop->index === 0 ? '1st' : ($loop->index === 1 ? '2nd' :'3rd')}}.webp">
                <div class="truncate @if($user->id === $ru->id) text-green-500 font-medium @endif">{{$ru->display_name}}</div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    @endforeach

  </div>
  <div id="panorama" class="flex-grow flex-shrink">
    panorama
  </div>
  <div class="w-80">
    @foreach($players as $player)
      @if(!$loop->first)
        <hr class="border-gray-700">
      @endif
      <div class="flex items-center px-4 py-2 rounded-md shadow-xl">
        <div class="text-center font-medium text-gray-500 text-2xl">{{$player->rank}}</div>
        <img class="h-12 ml-1" src="/static/img/map-marker/{{$player->map_marker_file_path}}"
             alt="Map Marker">
        <img class="w-12 shadow-md mx-1" src="/static/flag/svg/{{$player->country_cca2}}.svg"
             alt="Country Flag" tippy="{{$player->country_name}}">
        <div class="text-gray-300 ml-2 flex-grow">
          <div class="font-bold text-lg">
            {{$player->display_name}}
          </div>
          <div class="font-medium text-gray-400 flex justify-between items-center">
            {!! GameRoundResultRender::renderPoints(points: $player->points) !!}
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>
<script>
  @if($user->id === $players[0]->user_id)
  window.confetti({
    particleCount: 150,
  });
  @endif
</script>