<?php

declare(strict_types=1);

namespace Infrastructure\View\Enum;

enum ButtonStyle: string
{
	case PRIMARY = 'PRIMARY';
	case SECONDARY = 'SECONDARY';
	case INFO = 'INFO';
	case WARNING = 'WARNING';
	case ERROR = 'ERROR';
}
