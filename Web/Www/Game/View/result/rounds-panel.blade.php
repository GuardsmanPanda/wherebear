<?php declare(strict_types=1); 
  use Web\Www\Game\Util\GameUtil;
?>

<div class="bg-gray-50 select-none z-10">
  @foreach ($rounds as $round)
    <div class="flex flex-col p-2 bg-gray-50 relative" :class="{ 
      'bg-iris-300': '{{ $round->number }}' == selectedRoundNumberLargeScreen,
      'hover:bg-iris-100 cursor-pointer': '{{ $round->number }}' != selectedRoundNumberLargeScreen
      }" x-on:click="selectedRoundNumberLargeScreen !== {{ $round->number }} && selectRound({{ $round->number }})">
      <div class="flex justify-between items-center relative">
        <div class="flex gap-1">
          <span class="font-heading font-medium text-lg text-gray-500">{{ $round->number }}.</span>
          <span class="font-heading font-medium text-lg text-gray-800">{{ $round->country_name }}</span>
        </div>
        <div class="flex relative items-center place-self-start">
          <div class="relative">
            <img 
              src="/static/img/ui/ribbon-rank-{{ 
                $round->user_rank == 1 ? 'gold' : 
                ($round->user_rank == 2 ? 'silver' : 
                ($round->user_rank == 3 ? 'bronze' : 'default')) 
              }}.svg" 
              class="h-6" 
              alt="Rank Ribbon"
            />
            <div class="absolute inset-0 top-px flex gap-px justify-center items-center">
              <span class="z-10 font-heading font-semibold text-2xl text-gray-0 text-stroke-2 text-stroke-gray-700">{{ $round->user_rank }}</span>
              <span class="relative top-0.5 z-10 font-heading font-semibold text-sm text-gray-0 text-stroke-2 text-stroke-gray-700">{{ GameUtil::getOrdinalSuffix($round->user_rank) }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="flex gap-2">
        <img src="/static/flag/wavy/{{ strtolower($round->country_cca2) }}.png" alt="Flag of {{ $round->country_name }}" draggable="false" class="h-16" />
        <div class="flex flex-col gap-1">
          @foreach ($players as $key => $player)
            @break($key > 2)
            <div class="flex items-center gap-1 w-[148px]">
              <img
                src="/static/img/icon/medal-gray-{{ ['gold', 'silver', 'bronze'][$key] }}.svg"
                alt="{{ ['Gold', 'Silver', 'Bronze'][$key] }} Cup"
                class="h-5" 
              />
              <span class="text-sm text-gray-700 truncate">{{ $player->display_name }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>
    @if (!$loop->last)
      <div class="w-full h-px bg-gray-100"></div>
    @endif
  @endforeach
</div>