<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;

final class Heading extends Component {
  public function __construct(public string $label) {
  }

  public function render(): string {
    return <<<'blade'
    <div {{ $attributes->merge(['class' => 'flex justify-between items-center pb-1']) }}>
      <div class="flex items-center">
        <x-icon icon="chevron-right" :type="Web\Www\Shared\Enum\IconType::SOLID" size=5 color="text-shade-text-body" />
        <span class="font-heading text-sm text-shade-text-body uppercase">{{ $label }}</span>
      </div>
      <span>{{ $slot }}</span>
    </div>
    blade;
  }
}
