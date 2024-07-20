<?php declare(strict_types=1); ?>

<x-bear::dialog.basic>
    @foreach($grouped_map_markers as $group_name => $map_markers)
        <div>
            <div class="text-gray-700 text-xl font-bold border-b-2 @if(!$loop->first) mt-4 @endif">{{$group_name}}</div>
        </div>
        <div class="grid grid-cols-6 md:grid-cols-9" hx-target="#lobby">
            @foreach($map_markers as $marker)
                <button class="px-0.5 py-1 hover:scale-110 transition-transform duration-75" hx-dialog-close
                        hx-patch="/game/{{$game_id}}/lobby/update-user"
                        hx-vals='{"file_name": "{{$marker->file_name}}"}'
                        tippy="{{$marker->map_marker_name}}">
                    <img class="h-14 w-14" src="/static/img/map-marker/{{$marker->file_name}}"
                         alt="{{$marker->map_marker_name}}">
                </button>
            @endforeach
        </div>
    @endforeach
</x-bear::dialog.basic>
