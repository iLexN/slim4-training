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
     *
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
        /** @var string[] $args */
        $args = $event->getArgs();
        //dump($args);

        $this->cachePsr16();

        $this->cachePsr6();

        $this->cacheSF();

        $event = new ControllerEventAfter($args);
        $this->eventDispatcher->dispatch($event);
        $args = $event->getArgs();

        $response->getBody()->write('Hello1, ' . $args['name']);
        return $response;
    }

    public function cacheCallback(ItemInterface $item): Person
    {
        dump('no sf cache with tag');
        $item->tag('tag_1');
        return new Person('ilex 3', 'ilex3');
    }

    /**
     * @return void
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private function cachePsr16(): void
    {
        $person = $this->cache->get('person.ilex');
        if ($person === null) {
            $this->logger->info('no psr16 cache person');
            dump('no psr16 cache person');
            $person = new Person('ilex', 'ilex.job');
            $this->cache->set('person.ilex', $person);
        }
        //dump($person);
    }

    private function cachePsr6(): void
    {
        $p22 = $this->cacheItemPool->getItem('p22');
        if (!$p22->isHit()) {
            $aaa = '33333';
            $p22->set($aaa);
        }
    }

    private function cacheSF(): void
    {
        $p33 = $this->sfCache->get('person3', [$this, 'cacheCallback']);
        //dump($p33);
    }
}
