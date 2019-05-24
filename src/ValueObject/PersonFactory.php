<?php

declare(strict_types=1);

namespace App\ValueObject;

use Ilex\Slim\RouteStrategies\RouteArgsResolverInterface;
use Psr\Log\LoggerInterface;

final class PersonFactory implements RouteArgsResolverInterface
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getByName(string $name): Person
    {
        $this->logger->info($name);
        return new Person($name, 'name:' . $name);
    }

    public function getById(string $id): Person
    {
        return new Person($id, 'uid:' . $id);
    }

    public function getArgsResolver(): array
    {
        return [
            'name' => [$this, 'getByName'],
            'uid' => [$this, 'getById'],
        ];
    }
}
