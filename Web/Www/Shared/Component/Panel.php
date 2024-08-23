<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;

final class Panel extends Component {
  public function __construct() {
  }

  public function render(): string {
    return <<<'blade'
    <div {{ $attributes->class(['flex flex-col']) }}>
      <div class="flex flex-col gap-1 p-1 rounded bg-tertiary-surface-subtle border border-shade-border-default lg:gap-2 lg:rounded-md lg:p-2">
        @isset($heading)
        {{ $heading }}
        @endisset
        <div class="flex gap-2 rounded p-2 bg-tertiary-surface-default border border-shade-border-light lg:rounded-md lg:p-3">
        {{ $slot }}
        </div>
      </div>
    </div>
    blade;
  }
}
