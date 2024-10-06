<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;

final class CountryUsedList extends Component {
  /**
   * @param array<mixed> $countries
   */
  public function __construct(
    public array $countries,
    public int $currentRoundNumber,
    public int $totalRounds,
    public ?int $selectedRound = null
  ) {
  }

  public function getRankIcon(int $rank): ?string {
    return match ($rank) {
      1 => 'ribbon_gold',
      2 => 'ribbon_silver',
      3 => 'ribbon_bronze',
      default => null
    };
  }

  public function render(): string {
    return <<<'blade'
      <div class="flex flex-wrap w-full gap-1 bg-blue-500 justify-center p-2">
        @for($i = 0; $i < $totalRounds; $i++)
          <div class="relative">
            @if($i === $selectedRound - 1)
              <x-custom-icon icon="arrow" class="absolute -top-[20px] left-[5px] z-10 w-[30px]" style="filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.6));" />
            @endif
            @if($i < count($countries))
              <div tippy="{{ $countries[$i]->name }}">
              

              <x-country-used-icon :cca2="$countries[$i]->cca2" :name="$countries[$i]->name" />
              </div>
            @else
              <x-country-used-icon backgroundColor="{{ $i === $selectedRound - 1 ? 'bg-gray-50' : 'bg-gray-300' }}" :borderColor="'border-gray-900'" isPlaceholder="true" />
            @endif
          </div>
        @endfor
    </div>
    blade;
  }
}
/**
 * @if(in_array($countries[$i]->rank, [1, 2, 3]))
                <x-custom-icon :icon="$getRankIcon($countries[$i]->rank)" class="w-5 absolute -top-[1px] -right-[1px] z-20" />
              @endif
 */
