<?php

namespace Infrastructure\View\Components;

use Illuminate\View\Component;
use Infrastructure\View\Enum\UserLevelBadgeSize;

class UserLevelBadge extends Component
{
  public function __construct(public UserLevelBadgeSize $size, public int $level = 0) {}

  public function getImageUrl(): string
  {
    $filename = null;
    switch ($this->size) {
      case UserLevelBadgeSize::XS:
        $filename = 'level-badge-xs';
        break;
      case UserLevelBadgeSize::SM:
        $filename = 'level-badge-sm';
        break;
      case UserLevelBadgeSize::MD:
        $filename = 'level-badge-md';
        break;
      case UserLevelBadgeSize::LG:
        $filename = 'level-badge-lg';
    }
    return "/static/img/level-badge/{$filename}.png";
  }

  public function getBottomPx(): string
  {
    if ($this->size === UserLevelBadgeSize::LG) {
      return 'bottom-[8px]';
    }
    return 'bottom-[3px]';
  }

  public function getFontSize(): string
  {
    switch ($this->size) {
      case UserLevelBadgeSize::XS:
        return 'text-sm';
      case UserLevelBadgeSize::SM:
        return 'text-base';
      case UserLevelBadgeSize::MD:
        return 'text-lg';
      case UserLevelBadgeSize::LG:
        return 'text-3xl';
    }
  }

  public function render(): string
  {
    return <<<'blade'
		<div {{ $attributes() }} class="shrink-0">
			<div class='relative inline-block shrink-0'>
				<img src="{{ $getImageUrl() }}" class="block" alt="User level: {{ $level }}">
				<span class="flex items-center justify-center absolute inset-0 text-white  {{ $getBottomPx() }} {{ $getFontSize() }}">
					{{ $level }}
				</span>
			</div>
		</div>
		blade;
  }
}
