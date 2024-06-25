<?php

namespace NW\WebService\References\Operations\Notification\Abstract;

abstract class ReferencesOperation
{
    abstract public function doOperation(): array;

    protected function getRequest(string $param)
    {
        return $_REQUEST[$param] ?? null;
    }
}