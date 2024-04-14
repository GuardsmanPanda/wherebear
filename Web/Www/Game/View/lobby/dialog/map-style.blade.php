<?php declare(strict_types=1); ?>

<x-bear::dialog.basic>
    <div class="grid" hx-target="#primary">
        @foreach($map_styles as $style)
            <button class="px-0.5 py-1 hover:scale-110 transition-transform duration-75" hx-dialog-close
                    hx-patch="/game/{{$game_id}}/lobby/update-user"
                    hx-vals='{"map_marker_style_enum": "{{$style->map_style_enum}}"}'>
                <span class="text-xl font-medium">{{$style->map_style_name}}</span>
                <img class="h-24 w-96 object-none" src="/static/files/tile/{{$style->map_style_enum}}/11/1614/1016.png" alt="Example map tile">
            </button>
        @endforeach
    </div>
</x-bear::dialog.basic>
