<?php

declare(strict_types=1);

namespace App\Controller;

use App\Commands\ArticleCommand\ArticleSaveCommand;
use App\ValueObject\Address as Address1;
use App\ValueObject\Article\Article;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

final class Address
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(LoggerInterface $logger, CommandBus $commandBus)
    {
        $this->logger = $logger;
        $this->commandBus = $commandBus;
    }

    public function __invoke(
        ServerRequestInterface $serverRequest,
        ResponseInterface $response,
        Address1 $address1
    ): ResponseInterface {
        dump($address1);
        $this->logger->info('address controller', [$address1]);

        $article = new Article('title', 'desctiption', 1);
        $command = new ArticleSaveCommand($article);
        $this->commandBus->handle($command);

        $response->getBody()->write('Hello, ' . $address1->getAddress());
        return $response;
    }
}
