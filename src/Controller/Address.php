<?php

declare(strict_types=1);

namespace App\Controller;

use App\Commands\ArticleCommand\ArticleSaveCommand;
use App\Doctrine\User;
use App\ValueObject\Address as Address1;
use App\ValueObject\Article\Article;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Doctrine\UserRepository
     */
    private $userRepository;

    public function __construct(LoggerInterface $logger, CommandBus $commandBus, EntityManagerInterface $entityManager,\App\Doctrine\UserRepository $userRepository)
    {
        $this->logger = $logger;
        $this->commandBus = $commandBus;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
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

        $us = $this->userRepository->getLastUser(10);
        //$u = $this->entityManager->getRepository(User::class)->find(1);
        dump($us);


        $response->getBody()->write('Hello, ' . $address1->getAddress());
        return $response;
    }
}
