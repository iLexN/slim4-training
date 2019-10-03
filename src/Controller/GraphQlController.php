<?php

declare(strict_types=1);


namespace App\Controller;

use App\ValueObject\Person;
use GraphQL\GraphQL;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\CacheInterface as SymfonyCache;
use Symfony\Contracts\Cache\ItemInterface;
use TheCodingMachine\GraphQLite\Schema;
use TheCodingMachine\GraphQLite\SchemaFactory;

final class GraphQlController
{

    /**
     * @var \TheCodingMachine\GraphQLite\SchemaFactory
     */
    private $factory;

    public function __construct(
        SchemaFactory $factory
    ) {
        $this->factory = $factory;
    }

    public function __invoke(
        ServerRequestInterface $serverRequest,
        ResponseInterface $response
    ): ResponseInterface {

        $body = $serverRequest->getBody()->getContents();
        $payload = \json_decode($body, true);

        $query = $payload['query'];
        $variableValues = $payload['variable'] ?? null;

        $schema = $this->factory->createSchema();

        $result = GraphQL::executeQuery($schema,
            $query,
            null,
            null,
            $variableValues);

        $response->getBody()->write(\json_encode($result->toArray()));
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }
}
