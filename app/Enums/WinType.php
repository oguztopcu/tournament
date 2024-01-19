<?php

namespace App\Enums;

enum WinType: string {
    case UNKNOWN = 'unknown';

    case WIN = 'win';

    case LOSE = 'lose';

    case DRAW = 'draw';

    public static function getValues(): array {
        return [
            self::UNKNOWN->value,
            self::WIN->value,
            self::LOSE->value,
            self::DRAW->value,
        ];
    }
}