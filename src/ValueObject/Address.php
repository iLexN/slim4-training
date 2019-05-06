<?php

declare(strict_types=1);

namespace App\ValueObject;

final class Address
{
    /**
     * @var string
     */
    private $address;

    public function __construct(string $address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }
}
