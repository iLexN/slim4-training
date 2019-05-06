<?php

declare(strict_types=1);

namespace App\ValueObject;

final class Person
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getInfo(): string
    {
        return $this->getName() . '(' . $this->getEmail() . ')';
    }
}
