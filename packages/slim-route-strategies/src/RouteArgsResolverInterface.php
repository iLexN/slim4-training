<?php

declare(strict_types=1);

namespace Ilex\Slim\RouteStrategies;

interface RouteArgsResolverInterface
{
    /**
     * @return array
     */
    public function getArgsResolver(): array;
}
