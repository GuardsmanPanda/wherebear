<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;
use Web\Www\Shared\Enum\RewardType;

final class NextReward extends Component {

  public function __construct(public RewardType $type, public string $name, public string $iconUrl) {
  }

  public function getTypeLabel(): RewardType|string {
    return $this->type === RewardType::FEATURE ? 'feat' : $this->type;
  }

  public function getTypeBackgroundColor(): string {
    return match ($this->type) {
      RewardType::FEATURE => 'bg-blue-700',
      RewardType::FLAG => 'bg-orange-600',
      RewardType::ICON => 'bg-purple-700',
    };
  }

  public function render(): string {
    return <<<'blade'
    <div {{ $attributes->class(['flex flex-col items-end']) }}>
      <span class="text-xs text-shade-text-body pr-[60px] uppercase">Next reward</span>
      <div class="flex items-center">
        <img src="/static/img/ui/reward-left.png" />
        <div class="min-w-36 h-[18px] items-center bg-reward-surface-default border-y border-shade-border-dark relative">
          <div class="flex w-full h-full justify-end items-center gap-1 pl-4 py-0-5">
            <span class="px-1 text-xs leading-none text-white {{ $getTypeBackgroundColor() }} rounded-sm">{{ $getTypeLabel() }}</span>
            <span class="text-sm text-shade-text-title font-medium text-nowrap pr-14">{{ $name }}</span>
            <img src="{{ $iconUrl }}" alt="{{ $name }} {{ $type }}" class="max-w-10 max-h-10 absolute right-1 bottom-1" />
          </div>
        </div>
        <img src="/static/img/ui/reward-right.png" />
      </div>
    </div>
    blade;
  }
}
