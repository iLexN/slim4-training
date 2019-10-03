<?php

declare(strict_types=1);

namespace Domain\GraphQlQuery;

use Domain\Source\Model\SourceBusinessModel;
use Domain\Source\Repository\SourceRepository;
use Domain\Source\Repository\SourceRepositoryInterface;
use TheCodingMachine\GraphQLite\Annotations\Query;

final class QuerySource
{
    /**
     * @var SourceRepositoryInterface
     */
    private $repository;

    public function __construct(SourceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     *
     * @Query
     * @param int $id
     * @return SourceBusinessModel|null
     */
    public function getSourceById(int $id): ?SourceBusinessModel
    {
        try {
            return $this->repository->getOne($id);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    /**
     * cos will not load relationship, so can use cursor
     *
     * @Query()
     *
     * @return SourceBusinessModel[]|\Generator
     */
    public function getSourceList(): \Generator
    {
        foreach ($this->repository->getAll() as $s) {
            yield $s;
        }
    }
}
