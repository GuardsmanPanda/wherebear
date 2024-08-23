<?php

declare(strict_types=1);

namespace Web\Www\Shared\Component;

use Illuminate\View\Component;
use Web\Www\Shared\Enum\RewardType;

final class NextReward extends Component {

  /**
   * @param array<array<string, mixed>> $rewards
   */
  public function __construct(
    public int $level,
    public array $rewards
  ) {
  }

  public function shouldRender(): bool {
    return count($this->rewards) > 0;
  }

  public function getIconUrl(RewardType $rewardType, string $iconFilename): string {
    return RewardType::getIconUrl($rewardType, $iconFilename);
  }

  public function render(): string {
    return <<<'blade'
    <div {{ $attributes->class(['flex flex-col items-end']) }}>
      <div class="flex items-center">
        <img src="/static/img/ui/reward-left-sm.png" class="lg:hidden" />
        <img src="/static/img/ui/reward-left-lg.png" class="hidden lg:block" />
        <div class="flex h-4 lg:h-6 items-center gap-2 px-2 bg-reward-surface-default border-y border-shade-border-dark">
          <span class="text-xs lg:text-base text-shade-text-title font-medium">Level {{ $level }} Reward{{ count($rewards) > 1 ?  's' : '' }}</span>
          @foreach($rewards as $reward)
          <img src="{{ $getIconUrl($reward->type, $reward->iconFilename) }}" alt="{{ ucfirst(strtolower(str_replace('_', ' ', $reward->type->value))) }}" class="max-h-10 lg:max-h-16 relative bottom-[14px] drop-shadow-md" />
          @endforeach
        </div>
        <img src="/static/img/ui/reward-right-sm.png" class="lg:hidden" />
        <img src="/static/img/ui/reward-right-lg.png" class="hidden lg:block" />
      </div>
    </div>
    blade;
  }
}
