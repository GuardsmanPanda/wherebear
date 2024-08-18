<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;
use Web\Www\Shared\Enum\UserLevelBadgeSize;

final class UserLevelBadge extends Component {
  public function __construct(public UserLevelBadgeSize $size, public int $level = 0) {
  }

  public function getImageUrl(): string {
    $filename = match ($this->size) {
      UserLevelBadgeSize::XS => 'level-badge-xs',
      UserLevelBadgeSize::SM => 'level-badge-sm',
      UserLevelBadgeSize::MD => 'level-badge-md',
      UserLevelBadgeSize::LG => 'level-badge-lg',
    };
    return "/static/img/level-badge/$filename.png";
  }

  public function getBottomPx(): string {
    if ($this->size === UserLevelBadgeSize::LG) {
      return 'bottom-[8px]';
    }
    return 'bottom-[3px]';
  }

  public function getFontSize(): string {
    return match ($this->size) {
      UserLevelBadgeSize::XS => 'text-xs',
      UserLevelBadgeSize::SM => 'text-sm',
      UserLevelBadgeSize::MD => 'text-base',
      UserLevelBadgeSize::LG => 'text-2xl',
    };
  }

  public function render(): string {
    return <<<'blade'
		<div {{ $attributes() }} class="shrink-0">
			<div class='relative inline-block shrink-0'>
				<img src="{{ $getImageUrl() }}" class="block" alt="User level: {{ $level }}">
				<span class="flex items-center justify-center absolute inset-0 text-3xl text-shade-text-negative  {{ $getBottomPx() }} {{ $getFontSize() }}">
					{{ $level }}
				</span>
			</div>
		</div>
		blade;
  }
}