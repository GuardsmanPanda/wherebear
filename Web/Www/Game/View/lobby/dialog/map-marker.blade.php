<?php declare(strict_types=1); ?>

<x-bear::dialog.basic>
  @foreach($grouped_map_markers as $group_name => $map_markers)
    <div>
      <div class="text-gray-700 text-xl font-bold border-b-2 @if(!$loop->first) mt-4 @endif">{{$group_name}}</div>
    </div>
    <div class="grid grid-cols-6 md:grid-cols-9" hx-target="#lobby">
      @foreach($map_markers as $marker)
        <button class="py-1 mx-auto hover:scale-110 transition-transform duration-75" hx-dialog-close
                hx-patch="/game/{{$game_id}}/lobby/update-user"
                hx-vals='{"map_marker_enum": "{{$marker->enum}}"}'>
          <img class="drop-shadow h-14" src="{{$marker->file_path}}"
               alt="{{$marker->file_path}}">
        </button>
      @endforeach
    </div>
  @endforeach
</x-bear::dialog.basic>