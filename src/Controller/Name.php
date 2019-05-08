<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\ControllerEventAfter;
use App\Event\ControllerEventBefore;
use App\ValueObject\Person;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

final class Name
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var \Psr\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(LoggerInterface $logger, CacheInterface $cache, EventDispatcherInterface $dispatcher)
    {
        $this->logger = $logger;
        $this->cache = $cache;
        $this->dispatcher = $dispatcher;
    }


    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \App\ValueObject\Person $person1
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, Person $person1): ResponseInterface
    {
        $this->logger->info('input args', [$person1]);

        $event = new ControllerEventBefore(['name' => 'a']);
        $this->dispatcher->dispatch($event);
        $args = $event->getArgs();
        dump($args);

        $person = $this->cache->get('person.ilex');
        if (null === $person) {
            $this->logger->info('no cache person');
            dump('here have cache');
            $person = new Person('ilexn', 'ilex.job');
            $this->cache->set('person.ilex', $person);
        }

        $event = new ControllerEventAfter($args);
        $this->dispatcher->dispatch($event);
        $args = $event->getArgs();

        $response->getBody()->write('Hello, ' . $args['name']);
        return $response;
    }
}
