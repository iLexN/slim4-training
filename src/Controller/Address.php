<?php

declare(strict_types=1);

namespace App\Controller;

use App\ValueObject\Address as Address1;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class Address
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, Address1 $address): ResponseInterface
    {
        dump($address);
        $this->logger->info('address controller', [$address]);

        $response->getBody()->write('Hello, ' . $address->getAddress());
        return $response;
    }
}
