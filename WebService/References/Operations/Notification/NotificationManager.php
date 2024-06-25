<?php

namespace NW\WebService\References\Operations\Notification;

class NotificationManager
{
    public static function send(int $resellerId, int $clientId, string $eventType, int $statusChange, array $templateData, &$error = null): bool
    {
        echo "Sending notification to client {$clientId} for event '{$eventType}'\n";
    }
}