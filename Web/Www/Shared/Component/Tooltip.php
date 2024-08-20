<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;
use Web\Www\Shared\Enum\TooltipPosition;

final class Tooltip extends Component {
  public function __construct(
    public string $label,
    public TooltipPosition $position = TooltipPosition::TOP,
  ) {
  }

  public function getPositionClasses(): string {
    return match ($this->position) {
      TooltipPosition::TOP => 'bottom-full left-1/2 transform -translate-x-1/2 mb-1',
      TooltipPosition::BOTTOM => 'top-full left-1/2 transform -translate-x-1/2 mt-1',
      TooltipPosition::RIGHT => 'left-full ml-1',
      TooltipPosition::LEFT => 'right-full mr-1',
    };
  }

  public function render(): string {
    return <<<'blade'
    <div {{ $attributes->class(['relative', 'inline-black', 'group']) }}">{{ $slot }}
      @if($label)
      <span class="{{ $getPositionClasses }} absolute bg-shade-surface-light rounded text-xs text-shade-text-negative px-2 py-1 whitespace-nowrap hidden group-hover:block">
        {{ $label }}
      </span>
      @endif
    </div>
    blade;
  }
}
