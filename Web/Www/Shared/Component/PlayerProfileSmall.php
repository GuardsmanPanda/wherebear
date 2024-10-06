<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;

final class PlayerProfileSmall extends Component {
  public function __construct(
    public string  $name,
    public string  $flagFilePath,
    public string  $flagDescription,
    public string  $icon,
    public ?int    $level = null,
    public ?bool   $isActive = null,
    public ?string $nameBackgroundColor = 'bg-shade-surface-subtle',
    public ?string $textColor = 'text-shade-text-title',
  ) {
  }

  public function render(): string {
    return <<<'blade'
    <div {{ $attributes->class(['flex', 'flex-col', 'items-center']) }}>
      <div class="relative z-1">
        <div class="w-16 h-16 rounded-full overflow-hidden flex justify-center items-center bg-tertiary-surface-default border border-shade-border-dark rounded-full">
          <img class="h-16 w-auto object-cover object-center z-100" src="{{ $icon }}" alt="Map marker">
        </div>

        @isset($isActive)
        <div class="w-4 h-4 absolute top-0 right-0 rounded-full border {{ $isActive ? 'bg-success-surface-default border-success-border-dark' : 'bg-error-surface-default border-error-border-dark' }}"></div>
        @endisset
        
        @if ($level)
        <x-user-level-badge :size="Web\Www\Shared\Enum\UserLevelBadgeSize::XS" :level=$level class="absolute {{ $name ? '-bottom-1 -left-[6px]' : '-bottom-2' }} z-20" />
        @endif

        <img class="absolute bottom-1 -right-[6px] h-5 rounded-sm border border-shade-border-dark z-20" src="{{ $flagFilePath }}" alt="{{ $flagDescription }}" tippy="{{ $flagDescription }}" />
      </div>

      <div class="relative w-20 -mt-2 z-10 border border-shade-border-dark rounded px-1 py-0.5 {{ $nameBackgroundColor }} text-xs text-center {{ $textColor ?: 'text-shade-text-title' }} truncate">
        {{ $name }}
      </div>
    </div>
    blade;
  }
}
