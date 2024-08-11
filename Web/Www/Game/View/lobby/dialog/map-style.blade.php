<?php declare(strict_types=1); ?>
@php use Domain\Map\Enum\MapStyleEnum; @endphp

<x-bear::dialog.basic>
  <div class="grid" hx-target="#lobby">
    @foreach($map_styles as $style)
      <button class="px-0.5 py-1 hover:scale-110 transition-transform duration-75" hx-dialog-close
              hx-patch="/game/{{$game_id}}/lobby/update-user"
              hx-vals='{"map_style_enum": "{{$style->enum}}"}'>
        <span class="text-xl font-medium">{{$style->name}}</span>
        <img class="h-24 w-96 object-none" src="{{MapStyleEnum::from($style->enum)->mapTileUrl(z: 11, x: 1614, y: 1016)}}"
             alt="Example map tile">
      </button>
    @endforeach
  </div>
</x-bear::dialog.basic>
