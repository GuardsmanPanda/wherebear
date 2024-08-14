<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;

final class ButtonSelector extends Component {
  public function __construct(public string $label, public string $imageUrl) {
  }

  public function render(): string {
    return <<<'blade'
		<div {{ $attributes }} class="flex flex-col justify-between items-center gap-1">
			<span *ngIf="label" class="font-heading text-sm text-shade-text-subtitle">{{ $label }}</span>
			<button type="button" class="flex w-12 h-12 justify-center items-center p-1 rounded bg-secondary-surface-default border border-b-4 border-secondary-border-dark hover:bg-secondary-surface-dark active:h-[46px] active:border-t active:border-b-2">
				<img class="max-h-10 max-w-10" src="{{ $imageUrl }}" />
			</button>
		</div>
		blade;
  }
}
