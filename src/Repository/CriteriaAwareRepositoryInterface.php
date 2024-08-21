<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Repository;

use Doctrine\ORM\QueryBuilder;
use Ttskch\DoctrineOrmCriteria\Criteria\CriteriaInterface;

/**
 * @template T of object
 */
interface CriteriaAwareRepositoryInterface
{
    /**
     * @param array<CriteriaInterface>    $criteria
     * @param array<string, 'ASC'|'DESC'> $orderBy
     */
    public function createQueryBuilderByCriteria(array $criteria, array $orderBy = [], ?int $limit = null, ?int $offset = null): QueryBuilder;

    /**
     * @param array<CriteriaInterface>    $criteria
     * @param array<string, 'ASC'|'DESC'> $orderBy
     *
     * @return array<T>
     */
    public function findByCriteria(array $criteria, array $orderBy = [], ?int $limit = null, ?int $offset = null): array;

    /**
     * @param array<CriteriaInterface>    $criteria
     * @param array<string, 'ASC'|'DESC'> $orderBy
     *
     * @return ?T
     */
    public function findOneByCriteria(array $criteria, array $orderBy = []): ?object;

    /**
     * @param array<CriteriaInterface> $criteria
     */
    public function countByCriteria(array $criteria): int;
}
