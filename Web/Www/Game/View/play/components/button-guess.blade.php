<?php

declare(strict_types=1); ?>

<x-button-base heightPx="72" roundedCls="rounded" bgColorCls="bg-blue-500" bgColorHoverCls="bg-blue-600" bgColorSelectedCls="bg-blue-700" {{ $attributes }} class="w-[72px]">
  <div
    class="flex flex-col w-full h-full rounded border-2 border-t active:border-t-2 border-b-4 active:border-b-2 border-blue-700"
    style="box-shadow: inset 0 2px 1px rgb(255 255 255 / 0.2);">
    <img class="h-12" src="/static/img/map-icon/map.svg" draggable="false" />
    <span class="relative bottom-0.5 text-sm text-white font-medium">Guess</span>
  </div>
</x-button-base>