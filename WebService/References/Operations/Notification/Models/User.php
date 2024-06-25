<?php

namespace NW\WebService\References\Operations\Notification\Models;

class User
{
    public const USER_TYPE = 'default_user';
    protected $id;
    protected $name;
    protected $email;
    protected $mobile;

    public function getType(): string
    {
        return static::USER_TYPE;
    }

    public static function getById(int $id): ?self
    {
        return new self($id);// fakes the getById method
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getMobile(): string
    {
        return $this->mobile;
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->id;
    }
}