<?php

namespace NW\WebService\References\Operations\Notification\Models;

class Status
{
    public const COMPLETED = 'Completed';
    public const PENDING = 'Pending';
    public const REJECTED = 'Rejected';
    public static function getName(int $id): string
    {
        $statusNames = [
            0 => self::COMPLETED,
            1 => self::PENDING,
            2 => self::REJECTED,
        ];

        return $statusNames[$id] ?? 'Unknown';
    }
}
