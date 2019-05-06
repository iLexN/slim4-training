<?php

declare(strict_types=1);

namespace App\Controller;

use App\ValueObject\Person;
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

    public function __construct(LoggerInterface $logger, CacheInterface $cache)
    {
        $this->logger = $logger;
        $this->cache = $cache;
    }


    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, Person $person1): ResponseInterface
    {
        $this->logger->info('input args', [$person1]);

        $person = $this->cache->get('person.ilex');
        if (null === $person) {
            $this->logger->info('no cache person');
            $person = new Person('ilexn', 'ilex.job');
            $this->cache->set('person.ilex', $person);
        }

        $response->getBody()->write('Hello, ' . $person1->getInfo());
        return $response;
    }
}
