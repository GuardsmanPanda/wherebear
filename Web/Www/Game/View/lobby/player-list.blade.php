<?php declare(strict_types=1); ?>
<div class="bg-gray-900 px-4 py-4 rounded-md">
    <div class="flex justify-between items-center">
        <div class="flex items-center">
            <x-bear::icon name="users" size="6" class=" opacity-50 mr-2"/>
            <h2 class="text-xl font-bold">Players</h2>
        </div>
    </div>
    <hr class="mx-1 mt-1 mb-3 pb-0.5 border-gray-700 border-b-2 border-dashed">
    <ul>
        @foreach($players as $player)
            @if(!$loop->first)
                <hr class="mx-5 mt-1 mb-1 pb-0.5 border-gray-700 border-dashed">
            @endif
            <li class="flex items-center gap-2">
                <img class="h-8 w-8 flex-shrink-0" src="/static/img/map-marker/{{$player->file_name}}"
                     alt="{{$player->file_name}}">
                <img class="w-8 flex-shrink-0"
                     src="/static/flag/svg/{{$player->user_country_iso2_code}}.svg"
                     alt="{{$player->country_name}}" tippy="{{$player->country_name}}">
                <div class="w-full truncate md:max-w-48">
                    <span class="font-semibold">
                        {{$player->display_name}}
                    </span>
                    <div class="flex justify-between">
                        @if($player->is_ready)
                            <span class="text-green-400 text-xs -mt-1 font-medium">ready</span>
                        @else
                            <span class="text-gray-400 text-xs -mt-1">waiting</span>
                        @endif
                        @if(!$player->is_guest && $player->game_count === 1)
                            <span class="text-lime-400 font-medium text-xs -mt-1 pr-4">New Player!</span>
                        @elseif(!$player->is_guest)
                            <span class="text-green-400 text-xs -mt-1 pr-4">Played: {{$player->game_count - 1}}</span>
                        @else
                            <span class="text-gray-400 text-xs -mt-1 pr-4">Guest</span>
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>