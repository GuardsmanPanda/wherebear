<?php declare(strict_types=1);

namespace Domain\Game\Enum;

enum GameStateEnum: string {
    case WAITING_FOR_PLAYERS = 'WAITING_FOR_PLAYERS';
    case QUEUED = 'QUEUED';
    case STARTING = 'STARTING';
    case IN_PROGRESS = 'IN_PROGRESS';
    case IN_PROGRESS_CALCULATING = 'IN_PROGRESS_CALCULATING';
    case IN_PROGRESS_RESULT = 'IN_PROGRESS_RESULT';
    case FINISHED = 'FINISHED';
}
