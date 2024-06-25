<?php

namespace NW\WebService\References\Operations\Notification\Models;


use Grpc\Server;

class Contractor extends User
{
    public const USER_TYPE = 'contractor';
    public function getSeller():    Seller
    {
        return Seller::getByContractor($this->id);
    }


}

