<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

final class StopWatchMiddleware implements MiddlewareInterface
{
    public function __construct()
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $stopwatch = new StopWatch();
        $stopwatch->start('request');

        /** @var ResponseInterface $response */
        $response = $handler->handle($request);

        $event = $stopwatch->stop('request');
        dump($event->getDuration() . 'ms');

        return $response;
    }
}
