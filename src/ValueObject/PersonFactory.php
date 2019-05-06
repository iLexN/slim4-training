<?php

declare(strict_types=1);

namespace App\ValueObject;

use Ilex\Slim\RouteStrategies\RouteArgsResolverInterface;

final class PersonFactory implements RouteArgsResolverInterface
{
    public function getByName(string $name): Person
    {
        return new Person($name, 'name:' . $name);
    }

    public function getById(string $id): Person
    {
        return new Person($id, 'uid:' . $id);
    }

    public function getArgsResolver(): array
    {
        return [
            'name' => [$this, 'getByName1'],
            'uid' => [$this, 'getById'],
        ];
    }
}
