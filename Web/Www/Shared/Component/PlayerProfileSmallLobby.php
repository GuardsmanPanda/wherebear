<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;

final class PlayerProfileSmallLobby extends Component {
  public function __construct(
    public int     $level,
    public bool    $isReady,
    public string  $name = 'Guest',
    public bool    $isHost = false,
    public ?string $countryCode = null,
    public ?string $icon = null,
  ) {
  }

  public function getNameBackgroundColor(): string {
    return $this->isReady ? 'bg-success-surface-default' : 'bg-shade-surface-subtle';
    //if ($this->isHost) {
    //  return 'bg-reward-surface-default';
    //} else {
    //  return $this->isReady ? 'bg-success-surface-default' : 'bg-shade-surface-subtle';
    //}
  }

  public function render(): string {
    return <<<'blade'
    <x-player-profile-small :$name :$countryCode :$level :$icon :nameBackgroundColor="$getNameBackgroundColor()" />
    blade;
  }
}
