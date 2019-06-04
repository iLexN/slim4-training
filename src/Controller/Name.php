<?php

declare(strict_types=1);

namespace App\Controller;

use App\Event\ControllerEventAfter;
use App\Event\ControllerEventBefore;
use App\ValueObject\Person;
use Psr\Cache\CacheItemPoolInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\Cache\CacheInterface as SymfonyCache;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Cache\InvalidArgumentException;

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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var CacheItemPoolInterface
     */
    private $cacheItemPool;

    /**
     * @var \Symfony\Contracts\Cache\CacheInterface
     */
    private $sfCache;

    public function __construct(
        LoggerInterface $logger,
        CacheInterface $cache,
        EventDispatcherInterface $eventDispatcher,
        CacheItemPoolInterface $cacheItemPool,
        SymfonyCache $symfonyCache
    ) {
        $this->logger = $logger;
        $this->cache = $cache;
        $this->eventDispatcher = $eventDispatcher;
        $this->cacheItemPool = $cacheItemPool;
        $this->sfCache = $symfonyCache;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param ResponseInterface $response
     * @param Person $person
     *
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function __invoke(
        ServerRequestInterface $serverRequest,
        ResponseInterface $response,
        Person $person
    ): ResponseInterface {
        $this->logger->info('input args', [$person]);

        $event = new ControllerEventBefore(['name' => 'a']);
        $this->eventDispatcher->dispatch($event);
        $args = $event->getArgs();
        dump($args);

        $person = $this->cache->get('person.ilex');
        if (null === $person) {
            $this->logger->info('no psr16 cache person');
            dump('no psr16 cache person');
            $person = new Person('ilex', 'ilex.job');
            $this->cache->set('person.ilex', $person);
        }

        $p2 = $this->cacheItemPool->get('person2', static function (ItemInterface $item) {
            dump('no psr6 cache');
            return new Person('ilex2', 'ilex2');
        });
        dump($p2);

        $p3 = $this->sfCache->get('person3', [$this, 'cacheCallback']);
        dump($p3);


        $event = new ControllerEventAfter($args);
        $this->eventDispatcher->dispatch($event);
        $args = $event->getArgs();

        $response->getBody()->write('Hello, ' . $args['name']);
        return $response;
    }

    public function cacheCallback(ItemInterface $item): Person
    {
        dump('no sf cache with tag');
        $item->tag('tag_1');
        return new Person('ilex 3', 'ilex3');
    }
}
