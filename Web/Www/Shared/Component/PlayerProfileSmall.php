<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;

final class PlayerProfileSmall extends Component {
  public function __construct(
    public string  $name,
    public ?int    $level = null,
    public ?string $countryCode = null,
    public ?string $icon = null,
    public ?string $nameBackgroundColor = 'bg-primary-surface-default',
    public ?string $textColor = 'text-shade-text-title'
  ) {
  }

  public function render(): string {
    return <<<'blade'
    <div class="flex flex-col items-center">
      <div class="relative z-1">
        <div class="w-16 h-16 rounded-full overflow-hidden flex justify-center items-center bg-tertiary-surface-default border border-shade-border-dark rounded-full">
          <img class="h-16 w-auto object-cover object-center z-100" src="https://devfuntime.gman.bot/static/img/map-marker/{{ $icon ?? 'mario.png' }}" alt="User location marker on map">
        </div>
        
        @isset ($level)
        <x-user-level-badge :size="Web\Www\Shared\Enum\UserLevelBadgeSize::XS" :level=$level class="absolute -bottom-1 -left-[6px] z-20" />
        @endisset

        @isset ($countryCode)
        <img class="absolute bottom-1 -right-[6px] h-5 rounded-sm border border-shade-border-dark z-20" src="https://devfuntime.gman.bot/static/flag/svg/{{ $countryCode }}.svg" />
        @endif

      </div>
      <div class="relative w-20 -mt-2 z-10 border border-shade-border-dark rounded px-1 py-0.5 {{ $nameBackgroundColor }} text-xs text-center {{ $textColor ?: 'text-shade-text-title' }} truncate">
      {{ $name }}</div>
    </div>
    blade;
  }
}
