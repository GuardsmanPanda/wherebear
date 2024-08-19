<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;
use Web\Www\Shared\Enum\ButtonSize;
use Web\Www\Shared\Enum\ButtonStyle;
use Web\Www\Shared\Enum\ButtonType;
use Web\Www\Shared\Enum\IconPosition;
use Web\Www\Shared\Enum\IconType;

final class Button extends Component {
  public function __construct(
    public string       $label,
    public ButtonType   $type = ButtonType::PRIMARY,
    public ButtonStyle  $style = ButtonStyle::PRIMARY,
    public ButtonSize   $size = ButtonSize::SM,
    public IconPosition $iconPosition = IconPosition::NONE,
    public IconType     $iconType = IconType::SOLID,
    public bool         $isPill = false,
    public ?string      $icon = null,
  ) {
    if ($icon !== null && $iconPosition === IconPosition::NONE) {
      $this->iconPosition = IconPosition::LEFT;
    }
  }

  public function getHeightClass(): string {
    return match ($this->size) {
      ButtonSize::SM => 'h-[32px]',
      ButtonSize::MD => 'h-[40px]',
      ButtonSize::LG => 'h-[48px]',
    };
  }

  public function getActiveHeightClass(): string {
    return match ($this->size) {
      ButtonSize::SM => 'active:h-[28px]',
      ButtonSize::MD => 'active:h-[36px]',
      ButtonSize::LG => 'active:h-[44px]',
    };
  }

  private function getRoundedClass(): string {
    if ($this->isPill) {
      return "rounded-full";
    }

    return match ($this->type) {
      ButtonType::PRIMARY => 'rounded-md',
      ButtonType::SECONDARY => 'rounded'
    };
  }

  private function getBasePrimaryClasses(): string {
    $baseClasses = ' border-x-[1px] text-xl text-white font-bold uppercase';

    return $baseClasses . match ($this->style) {
      ButtonStyle::PRIMARY => ' bg-primary-surface-default border-primary-border-default shadow-[inset_0_-5px_0_#689F38]
        hover:bg-primary-surface-dark hover:border-primary-border-dark hover:shadow-[inset_0_-5px_0_#558B2F] 
        active:border-t active:shadow-[inset_0_-2px_0_#558B2F]',
      ButtonStyle::SECONDARY => ' bg-secondary-surface-default border-secondary-border-default shadow-[inset_0_-5px_0_#6d83cd]
        hover:bg-secondary-surface-dark hover:border-secondary-border-dark hover:shadow-[inset_0_-5px_0_#3953a7] 
        active:border-t active:shadow-[inset_0_-2px_0_#3953a7]',
      ButtonStyle::INFO => ' bg-info-surface-subtle hover:bg-info-surface-light border-info-border-subtle hover:border-info-border-light text-info-text',
      ButtonStyle::WARNING => ' bg-warning-surface-subtle hover:bg-warning-surface-light border-warning-border-subtle hover:border-warning-border-light text-warning-text',
      ButtonStyle::ERROR => ' bg-error-surface-default border-error-border-default shadow-[inset_0_-5px_0_#8F354C]
        hover:bg-error-surface-dark hover:border-error-border-dark hover:shadow-[inset_0_-5px_0_#602031] 
        active:border-t active:shadow-[inset_0_-2px_0_#602031]',
    };
  }

  private function getBaseSecondaryClasses(): string {
    $baseClasses = ' border-[1px] border-b-[3px] active:border-b-[1px] font-medium';

    return $baseClasses . match ($this->style) {
      ButtonStyle::PRIMARY => ' bg-primary-surface-subtle border-primary-border-subtle text-primary-text hover:bg-primary-surface-light hover:border-primary-border-light',
      ButtonStyle::SECONDARY => ' bg-secondary-surface-subtle hover:bg-secondary-surface-light border-secondary-border-subtle hover:border-secondary-border-light text-secondary-text',
      ButtonStyle::INFO => ' bg-info-surface-subtle hover:bg-info-surface-light border-info-border-subtle hover:border-info-border-light text-info-text',
      ButtonStyle::WARNING => ' bg-warning-surface-subtle hover:bg-warning-surface-light border-warning-border-subtle hover:border-warning-border-light text-warning-text',
      ButtonStyle::ERROR => ' bg-error-surface-subtle hover:bg-error-surface-light border-error-border-subtle hover:border-error-border-light text-error-text',
    };
  }


  public function getDynamicClasses(): string {
    $classes = match ($this->type) {
      ButtonType::PRIMARY => $this->getBasePrimaryClasses(),
      ButtonType::SECONDARY => $this->getBaseSecondaryClasses(),
    };

    $classes .= " {$this->getRoundedClass()} {$this->getHeightClass()} {$this->getActiveHeightClass()}";

    return $classes;
  }

  public function render(): string {
    return <<<'blade'
    <button {{ $attributes->class([
      'flex', 
      'items-end',
      $getHeightClass(), 
    ]) }}>
      <div class="flex justify-center items-center w-full gap-2 font-body text-md {{ $getDynamicClasses() }}">
        @if($iconPosition === Web\Www\Shared\Enum\IconPosition::LEFT)
        <x-icon icon="{{ $icon }}" :type="$iconType" color="white" />
        @endif
            
        @if($iconPosition == Web\Www\Shared\Enum\IconPosition::ONLY)
        <x-icon icon="{{ $icon }}" :type="$iconType" color="white" />
        @else
        <span>{{ $label }}</span>
        @endif

        @if($iconPosition === Web\Www\Shared\Enum\IconPosition::RIGHT)
        <x-icon icon="{{ $icon }}" :type="$iconType" color="white" />
        @endif
      </div>
    </button>
    blade;
  }
}
