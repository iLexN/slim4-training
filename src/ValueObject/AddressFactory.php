<?php

declare(strict_types=1);

namespace App\ValueObject;

use Ilex\Slim\RouteStrategies\RouteArgsResolverInterface;

final class AddressFactory implements RouteArgsResolverInterface
{
    public function get(string $value): Address
    {
        return new Address($value);
    }

    public function getArgsResolver(): iterable
    {
        return [
            'address' => [$this, 'get'],
            'aid' => [$this, 'get'],
        ];
    }
}
