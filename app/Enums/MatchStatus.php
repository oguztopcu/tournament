<?php

namespace App\Enums;

enum MatchStatus: string {
    case PENDING = 'pending';

    case FINISHED = 'finished';

    public static function getValues(): array {
        return [
            self::PENDING->value,
            self::FINISHED->value
        ];
    }
}