<?php

namespace NW\WebService\References\Operations\Notification\Models;

use NW\WebService\References\Operations\Notification\Models\Contractor;

class Seller extends User
{
    public const USER_TYPE = 'seller';

    public static function getByContractor($id)
    {
        return new self($id); // fakes the getByContractor method
    }
}