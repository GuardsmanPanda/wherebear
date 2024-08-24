<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;

final class PlayerProfileLargeLobby extends Component {
  public function __construct(
    public int     $level,
    public bool    $isReady,
    public string  $name,
    public string  $flagFilePath,
    public string  $flagDescription,
    public string  $icon,
    public bool    $isHost = false,
    public ?string $title = null,
  ) {
  }

  public function getNameBackgroundColor(): string {
    return $this->isReady ? 'bg-success-surface-light' : 'bg-shade-surface-subtle';
  }

  public function render(): string {
    return <<<'blade'
    <div {{ $attributes->class(['flex', 'gap-2', 'items-center']) }}">
      <div class="flex w-12 justify-center items-center">
      <img class="max-w-12 max-h-12 w-auto drop-shadow-md" src="/static/img/map-marker/{{ $icon }}" alt="Map marker">
      </div>
      <div class="flex flex-col flex-1 {{ $level ? 'gap-1' : 'gap-2' }} truncate">
        <span class="font-heading text-xl text-shade-text-title leading-none truncate">{{ $name }}</span>
        <div class="flex items-center gap-1">
          @if($level)
           <x-user-level-badge :size="Web\Www\Shared\Enum\UserLevelBadgeSize::XS" :level=$level />
          @endif
          <span class="text-sm text-shade-text-subtitle leading-none truncate">{{ $title }}</span>
        </div>
      </div>
      <img class="h-10 rounded shrink-0 border border-shade-border-light" src="{{ $flagFilePath }}" alt="{{ $flagDescription }}" tippy="{{ $flagDescription }}" />
    </div>
    blade;
  }
}
