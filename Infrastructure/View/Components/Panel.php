<?php

namespace Infrastructure\View\Components;

use Illuminate\View\Component;

class Panel extends Component
{
  public function __construct() {}

  public function render(): string
  {
    return <<<'blade'
    <div {{ $attributes->class(['flex flex-col gap-1']) }}>
      <div class="flex flex-col p-1 rounded bg-tertiary-surface-subtle border border-shade-border-default">
        @isset($heading)
        {{ $heading }}
        @endisset
        <div class="flex gap-2 rounded p-2 bg-tertiary-surface-light border border-shade-border-light">
        {{ $slot }}
        </div>
      </div>
    </div>
    blade;
  }
}
