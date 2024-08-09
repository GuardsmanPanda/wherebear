<?php

declare(strict_types=1);

namespace Infrastructure\View\Components;

use Illuminate\View\Component;

class ButtonSelector extends Component
{
  public function __construct(public string $label, public string $imageUrl) {}


  public function render(): string
  {
    return <<<'blade'
		<div {{ $attributes }} class="flex flex-col items-center gap-1">
			<span *ngIf="label" class="font-heading text-sm text-shade-text-subtitle">{{ $label }}</span>
			<button type="button" class="flex w-12 h-12 justify-center items-center p-1 rounded bg-secondary-surface-default hover:bg-secondary-surface-dark border border-b-2 border-secondary-border-dark">
				<img class="max-h-10 max-w-10" src="{{ $imageUrl }}" />
			</button>
		</div>
		blade;
  }
}
