<?php

namespace NW\WebService\References\Operations\Notification;

class MessagesClient
{
    public static function sendMessage(array $messageData, int $resellerId, string $eventType, string|null $newStatus = null, int|null $clientId = null)
    {
        //Просто заглушка
        echo "Sending email to {$messageData[0]['emailTo']} with subject '{$messageData[0]['subject']}'\n";
    }
}