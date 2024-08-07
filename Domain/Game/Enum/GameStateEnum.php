<?php declare(strict_types=1);

namespace Domain\Game\Enum;

use Domain\Game\Crud\GameStateCreator;
use Domain\Game\Model\GameState;
use Domain\Game\Service\GameStateService;

enum GameStateEnum: string {
    case WAITING_FOR_PLAYERS = 'WAITING_FOR_PLAYERS';
    case QUEUED = 'QUEUED';
    case STARTING = 'STARTING';
    case IN_PROGRESS = 'IN_PROGRESS';
    case IN_PROGRESS_CALCULATING = 'IN_PROGRESS_CALCULATING';
    case IN_PROGRESS_RESULT = 'IN_PROGRESS_RESULT';
    case FINISHED = 'FINISHED';

    public function getDescription(): string {
        return match ($this) {
            self::WAITING_FOR_PLAYERS => 'Waiting for players',
            self::QUEUED => 'Queued',
            self::STARTING => 'Starting',
            self::IN_PROGRESS => 'In progress',
            self::IN_PROGRESS_CALCULATING => 'In progress calculating',
            self::IN_PROGRESS_RESULT => 'In progress result',
            self::FINISHED => 'Finished',
        };
    }

    public function isStarting(): bool {
        return $this === self::WAITING_FOR_PLAYERS || $this === self::QUEUED || $this === self::STARTING;
    }

    public function isInProgress(): bool {
        return $this === self::IN_PROGRESS || $this === self::IN_PROGRESS_CALCULATING || $this === self::IN_PROGRESS_RESULT;
    }

    public function isFinished(): bool {
        return $this === self::FINISHED;
    }


    public static function syncToDatabase(): void {
        foreach (GameStateEnum::cases() as $enum) {
            GameStateCreator::syncToDatabase(enum: $enum);
        }
    }
}
