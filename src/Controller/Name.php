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
     * @var \Psr\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var \Psr\Cache\CacheItemPoolInterface
     */
    private $psr6;

    /**
     * @var \Symfony\Contracts\Cache\CacheInterface
     */
    private $sfCache;

    public function __construct(
        LoggerInterface $logger,
        CacheInterface $cache,
        EventDispatcherInterface $dispatcher,
        CacheItemPoolInterface $psr6,
        SymfonyCache $sfCache
    ) {
        $this->logger = $logger;
        $this->cache = $cache;
        $this->dispatcher = $dispatcher;
        $this->psr6 = $psr6;
        $this->sfCache = $sfCache;
    }


    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \App\ValueObject\Person $person1
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Person $person1
    ): ResponseInterface {
        $this->logger->info('input args', [$person1]);

        $event = new ControllerEventBefore(['name' => 'a']);
        $this->dispatcher->dispatch($event);
        $args = $event->getArgs();
        dump($args);

        $person = $this->cache->get('person.ilex');
        if (null === $person) {
            $this->logger->info('no cache person');
            dump('here create cache');
            $person = new Person('ilexn', 'ilex.job');
            $this->cache->set('person.ilex', $person);
        }

        $p2 = $this->psr6->get('person2', function (ItemInterface $item) {
            dump('cache callback');
            return new Person('ilexn2', 'ilex2');
        });
        dump($p2);

        $p3 = $this->sfCache->get('person3', [$this,'cacheCallback']);
        dump($p3);


        $event = new ControllerEventAfter($args);
        $this->dispatcher->dispatch($event);
        $args = $event->getArgs();

        $response->getBody()->write('Hello, ' . $args['name']);
        return $response;
    }

    public function cacheCallback(ItemInterface $item): Person
    {
        dump(__METHOD__);
        $item->tag('tag_1');
        return new Person('ilexn3', 'ilex3');
    }
}
