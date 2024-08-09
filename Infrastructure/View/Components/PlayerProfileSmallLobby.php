<?php

namespace Infrastructure\View\Components;

use Illuminate\View\Component;

class PlayerProfileSmallLobby extends Component
{
  public function __construct(
    public string $name = 'Guest',
    public int $level,
    public bool $isReady,
    public bool $isHost = false,
    public ?string $countryCode = null,
    public ?string $icon = null,
  ) {}

  public function getNameBgColor(): string
  {
    return $this->isReady ? 'bg-success-surface-default' : 'bg-shade-surface-subtle';
    if ($this->isHost) {
      return 'bg-reward-surface-default';
    } else {
      return $this->isReady ? 'bg-success-surface-default' : 'bg-shade-surface-subtle';
    }
  }

  public function render(): string
  {
    return <<<'blade'
    <x-player-profile-small :$name :$countryCode :$level :$icon :nameBgColor="$getNameBgColor()" />
    blade;
  }
}
