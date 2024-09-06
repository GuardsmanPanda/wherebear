<?php

declare(strict_types=1); ?>

<div class="flex flex-wrap w-full gap-1 bg-blue-500 justify-center p-2">
  @foreach($countries_used as $country)
  <x-country-used-icon :cca2="$country->cca2" :name="$country->name" />
  @endforeach

  @for($i = 0; $i < $game->number_of_rounds - count($countries_used); $i++)
    @if($i==0)
    <div class="relative">
      <x-country-used-icon backgroundColor="bg-gray-50" :borderColor="'border-gray-900'" isPlaceholder="true" />
      <x-arrow class="absolute -top-[20px] left-[4px] w-[30px]" style="filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.6));" />
    </div>
    @else
    <x-country-used-icon backgroundColor="bg-gray-300" :borderColor="'border-gray-900'" isPlaceholder="true" />
    @endif
    @endfor
</div>