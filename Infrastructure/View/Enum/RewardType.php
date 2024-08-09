<?php

declare(strict_types=1);

namespace Infrastructure\View\Enum;

enum RewardType: string
{
  case FEATURE = 'FEATURE';
  case FLAG = 'FLAG';
  case ICON = 'ICON';
}
