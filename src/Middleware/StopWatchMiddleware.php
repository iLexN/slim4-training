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

    public function process(
        ServerRequestInterface $serverRequest,
        RequestHandlerInterface $requestHandler
    ): ResponseInterface {
        $stopwatch = new Stopwatch();
        $stopwatch->start('request');

        /** @var ResponseInterface $response */
        $response = $requestHandler->handle($serverRequest);

        $event = $stopwatch->stop('request');
        dump($event->getDuration() . 'ms');

        return $response;
    }
}
