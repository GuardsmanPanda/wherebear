<?php

namespace Infrastructure\View\Components;

use Illuminate\View\Component;
use Infrastructure\View\Enum\ButtonSize;
use Infrastructure\View\Enum\ButtonStyle;
use Infrastructure\View\Enum\ButtonType;
use Infrastructure\View\Enum\IconPosition;
use Infrastructure\View\Enum\IconType;

class Button extends Component
{

  private int $HEIGHT_PX_SM = 32;
  private int $HEIGHT_PX_MD = 40;
  private int $HEIGHT_PX_LG = 48;

  public int $heightPx;

  public function __construct(
    public string $label,
    public ButtonType $type = ButtonType::PRIMARY,
    public ButtonStyle $style = ButtonStyle::PRIMARY,
    public ButtonSize $size = ButtonSize::SM,
    public IconPosition $iconPosition = IconPosition::NONE,
    public IconType $iconType = IconType::SOLID,
    public ?int $widthPx = null,
    public ?string $icon = null,
  ) {
    if ($icon && $iconPosition === IconPosition::NONE) {
      $this->iconPosition = IconPosition::LEFT;
    }

    switch ($size) {
      case ButtonSize::SM:
        $this->heightPx = $this->HEIGHT_PX_SM;
        break;
      case ButtonSize::MD:
        $this->heightPx = $this->HEIGHT_PX_MD;
        break;
      case ButtonSize::LG:
        $this->heightPx = $this->HEIGHT_PX_LG;
        break;
      default:
        $this->heightPx = $this->HEIGHT_PX_SM;
        break;
    }
  }

  private function getDynamicPrimaryClasses(): string
  {
    $activeHeightPx = $this->heightPx - 2;
    $classes = " h-[{$this->heightPx}px] active:h-[{$activeHeightPx}px] border-x-[1px] rounded-md text-xl text-white font-bold uppercase";

    switch ($this->style) {
      case ButtonStyle::PRIMARY:
        $classes .= ' bg-primary-surface-default border-primary-border-default shadow-[inset_0_-5px_0_#689F38]
        hover:bg-primary-surface-dark hover:border-primary-border-dark hover:shadow-[inset_0_-5px_0_#558B2F] 
        active:border-t active:shadow-[inset_0_-2px_0_#558B2F]';
        break;
      case ButtonStyle::INFO:
        $classes .= ' bg-info-surface-subtle hover:bg-info-surface-light border-info-border-subtle hover:border-info-border-light text-info-text';
        break;
      case ButtonStyle::WARNING:
        $classes .= ' bg-warning-surface-subtle hover:bg-warning-surface-light border-warning-border-subtle hover:border-warning-border-light text-warning-text';
        break;
      case ButtonStyle::ERROR:
        $classes .= ' bg-error-surface-default border-error-border-default shadow-[inset_0_-5px_0_#8F354C]
        hover:bg-error-surface-dark hover:border-error-border-dark hover:shadow-[inset_0_-5px_0_#602031] 
        active:border-t active:shadow-[inset_0_-2px_0_#602031]';
        break;
    }

    return $classes;
  }

  private function getDynamicSecondaryClasses(): string
  {
    $activeHeightPx = $this->heightPx - 2;
    $classes = " h-[{$this->heightPx}px] active:h-[{$activeHeightPx}px] border-[1px] border-b-[3px] active:border-b-[1px] rounded";

    switch ($this->style) {
      case ButtonStyle::PRIMARY:
        $classes .= ' bg-primary-surface-subtle border-primary-border-subtle text-primary-text hover:bg-primary-surface-light hover:border-primary-border-light';
        break;
      case ButtonStyle::INFO:
        $classes .= ' bg-info-surface-subtle hover:bg-info-surface-light border-info-border-subtle hover:border-info-border-light text-info-text';
        break;
      case ButtonStyle::WARNING:
        $classes .= ' bg-warning-surface-subtle hover:bg-warning-surface-light border-warning-border-subtle hover:border-warning-border-light text-warning-text';
        break;
      case ButtonStyle::ERROR:
        $classes .= ' bg-error-surface-subtle hover:bg-error-surface-light border-error-border-subtle hover:border-error-border-light text-error-text';
    }

    return $classes;
  }

  public function getDynamicClasses(): string
  {
    if ($this->type === ButtonType::PRIMARY) return $this->getDynamicPrimaryClasses();
    return $this->getDynamicSecondaryClasses();
  }

  public function render(): string
  {
    return <<<'blade'
      <button {{ $attributes->class([
          'flex',
          'justify-center',
          'items-center',
          'gap-2',
          'w-['.$widthPx.'px]' => $widthPx,
          'font-body',
          'text-md',
          $getDynamicClasses()
        ]) }}>
          @if($iconPosition === Infrastructure\View\Enum\IconPosition::LEFT)
            <x-icon icon="{{ $icon }}" :type="$iconType" color="white" />
            @endif
            
          @if($iconPosition == Infrastructure\View\Enum\IconPosition::ONLY)
            <x-icon icon="{{ $icon }}" :type="$iconType" color="white" />
          @else
            <span>{{ $label }}</span>
          @endif

          @if($iconPosition === Infrastructure\View\Enum\IconPosition::RIGHT)
            <x-icon icon="{{ $icon }}" :type="$iconType" color="white" />
          @endif
        </button>
      blade;
  }
}
