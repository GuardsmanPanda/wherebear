<?php declare(strict_types=1); ?>
<div class="absolute bg-black bg-opacity-70 font-bold left-9 px-4 py-0.5 rounded-b-md text-gray-400 text-2xl z-20 shadow-lg" tippy="Photographed: Year-Month">
    {{substr(string: $game->captured_date, offset: 0, length: 7)}}
</div>

<div class="absolute bottom-7 drop-shadow-lg filter font-bold origin-bottom-left pointer-events-none scale-75 transform z-20">
    <div class="relative">
        <img src="/static/img/pengu-sign.png" class="h-52" alt="Cutest pengu around">
        <div class="absolute text-gray-800 opacity-70 rotate-1 text-xl text-center top-1 transform w-full">Game Round</div>
        <div class="text-gray-800 text-3xl leading-7 tabular-nums absolute top-6 w-full text-center">{{$game->current_round}}/{{$game->number_of_rounds}}</div>
    </div>
</div>

<script>
    pannellum.viewer('play', {
        "type": "equirectangular",
        "panorama": "https://panorama.gman.bot/{{$game->jpg_path}}",
        "autoLoad": true,
    });
</script>