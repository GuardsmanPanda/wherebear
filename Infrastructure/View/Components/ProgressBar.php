<?php

namespace Infrastructure\View\Components;

use Illuminate\View\Component;

class ProgressBar extends Component
{
  public function __construct(public int $percentage) {}

  public function render(): string
  {
    return <<<'blade'
      <div {{ $attributes->class(['flex w-full h-4 bg-shade-surface-subtle border border-shade-border-default rounded-full']) }}>
        <div class="bg-primary-surface-default border border-l-0 border-shade-border-default rounded-full" style="width: {{$percentage}}%"></div>
      </div>
    blade;
  }
}
