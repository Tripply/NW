<?php

namespace NW\WebService\References\Operations\Notification;

use NW\WebService\References\Operations\Notification\Abstract\ReferencesOperation;
use NW\WebService\References\Operations\Notification\Models\{Contractor, Employee, Seller, Status};
use NW\WebService\References\Operations\Notification\Events\NotificationEvents;

class TsReturnOperation extends ReferencesOperation
{
    public const TYPE_NEW    = 1;
    public const TYPE_CHANGE = 2;

    /**
     * @throws \Exception
     */
    public function doOperation(): array
    {
        $result = [
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail'   => false,
            'notificationClientBySms'     => [
                'isSent'  => false,
                'message' => '',
            ],
        ];

        $data = (array) $this->getRequest('data');
        $resellerId = (int) ($data['resellerId'] ?? 0);
        $notificationType = (int) ($data['notificationType'] ?? 0);

        if (empty($resellerId)) {
            $result['notificationClientBySms']['message'] = 'Empty resellerId';
            return $result;
        }

        if (empty($notificationType)) {
            throw new \Exception('Empty notificationType', 400);
        }

        $reseller = Seller::getById($resellerId);
        if (!$reseller) {
            throw new \Exception('Seller not found!', 400);
        }

        $client = Contractor::getById((int) ($data['clientId'] ?? 0));
        if (!$client || $client->getType() !== Contractor::USER_TYPE || $client->getSeller()->id !== $resellerId) {
            throw new \Exception('Client not found!', 400);
        }

        $cFullName = $client->getFullName();
        if (empty($cFullName)) {
            $cFullName = 'Unknown';
        }

        $cr = Employee::getById((int) ($data['creatorId'] ?? 0));
        if (!$cr) {
            throw new \Exception('Creator not found!', 400);
        }

        $et = Employee::getById((int) ($data['expertId'] ?? 0));
        if (!$et) {
            throw new \Exception('Expert not found!', 400);
        }

        $differences = '';
        if ($notificationType === self::TYPE_NEW) {
            $differences = __('NewPositionAdded', null, $resellerId);
        } elseif ($notificationType === self::TYPE_CHANGE && isset($data['differences'])) {
            $differences = __('PositionStatusHasChanged', [
                'FROM' => Status::getName((int) ($data['differences']['from'] ?? 0)),
                'TO'   => Status::getName((int) ($data['differences']['to'] ?? 0)),
            ], $resellerId);
        }

        $templateData = [
            'COMPLAINT_ID'       => (int) ($data['complaintId'] ?? 0),
            'COMPLAINT_NUMBER'   => (string) ($data['complaintNumber'] ?? ''),
            'CREATOR_ID'         => (int) ($data['creatorId'] ?? 0),
            'CREATOR_NAME'       => $cr->getFullName(),
            'EXPERT_ID'          => (int) ($data['expertId'] ?? 0),
            'EXPERT_NAME'        => $et->getFullName(),
            'CLIENT_ID'          => (int) ($data['clientId'] ?? 0),
            'CLIENT_NAME'        => $cFullName,
            'CONSUMPTION_ID'     => (int) ($data['consumptionId'] ?? 0),
            'CONSUMPTION_NUMBER' => (string) ($data['consumptionNumber'] ?? ''),
            'AGREEMENT_NUMBER'   => (string) ($data['agreementNumber'] ?? ''),
            'DATE'               => (string) ($data['date'] ?? ''),
            'DIFFERENCES'        => $differences,
        ];

        foreach ($templateData as $key => $tempData) {
            if (empty($tempData)) {
                throw new \Exception("Template Data ({$key}) is empty!", 500);
            }
        }

        $emailFrom = $this->getResellerEmailFrom($resellerId);
        $emails = $this->getEmailsByPermit($resellerId, 'tsGoodsReturn');

        if (!empty($emailFrom) && count($emails) > 0) {
            foreach ($emails as $email) {
                MessagesClient::sendMessage([
                    0 => [
                        'emailFrom' => $emailFrom,
                        'emailTo'   => $email,
                        'subject'   => __('complaintEmployeeEmailSubject', $templateData, $resellerId),
                        'message'   => __('complaintEmployeeEmailBody', $templateData, $resellerId),
                    ],
                ], $resellerId, NotificationEvents::CHANGE_RETURN_STATUS);
                $result['notificationEmployeeByEmail'] = true;
            }
        }

        if ($notificationType === self::TYPE_CHANGE && !empty($data['differences']['to'])) {
            if (!empty($emailFrom) && !empty($client->getEmail())) {
                MessagesClient::sendMessage([
                    0 => [
                        'emailFrom' => $emailFrom,
                        'emailTo'   => $client->getEmail(),
                        'subject'   => __('complaintClientEmailSubject', $templateData, $resellerId),
                        'message'   => __('complaintClientEmailBody', $templateData, $resellerId),
                    ],
                ], $resellerId, NotificationEvents::CHANGE_RETURN_STATUS, (int) ($data['differences']['to'] ?? 0));
                $result['notificationClientByEmail'] = true;
            }

            if (!empty($client->getMobile())) {
                NotificationManager::send($resellerId, $client->getId(), NotificationEvents::CHANGE_RETURN_STATUS, (int) ($data['differences']['to'] ?? 0), $templateData);
                $result['notificationClientBySms']['isSent'] = true;
            }
        }

        return $result;
    }

    function getResellerEmailFrom()
    {
        return 'contractor@example.com';
    }

    function getEmailsByPermit($resellerId, $event)
    {
        // fakes the method
        return ['someemeil@example.com', 'someemeil2@example.com'];
    }
}
