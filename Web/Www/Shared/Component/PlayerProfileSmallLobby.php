<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;

final class PlayerProfileSmallLobby extends Component {
  public function __construct(
    public int     $level,
    public bool    $isReady,
    public string  $name,
    public string  $countryCode,
    public string  $countryName,
    public string  $icon,
    public bool    $isHost = false,
  ) {
  }

  public function getNameBackgroundColor(): string {
    return $this->isReady ? 'bg-success-surface-light' : 'bg-shade-surface-subtle';
  }

  public function render(): string {
    return <<<'blade'
    <x-player-profile-small {{ $attributes }} :$name :$countryCode :$countryName :$level :$icon :nameBackgroundColor="$getNameBackgroundColor()" />
    blade;
  }
}
