<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Repository;

use Doctrine\ORM\QueryBuilder;
use Ttskch\DoctrineOrmCriteria\Criteria\CriteriaInterface;
use Ttskch\DoctrineOrmCriteria\CriteriaAwareness;

/**
 * @template T of object
 *
 * @method QueryBuilder createQueryBuilder(string $alias, ?string $indexBy = null)
 */
trait CriteriaAwareRepositoryTrait
{
    /**
     * @param array<CriteriaInterface>    $criteria
     * @param array<string, 'ASC'|'DESC'> $orderBy
     */
    public function createQueryBuilderByCriteria(array $criteria, array $orderBy = [], ?int $limit = null, ?int $offset = null): QueryBuilder
    {
        $ca = (new CriteriaAwareness($this->createQueryBuilder('entity')));

        foreach ($criteria as $criterion) {
            $ca->addCriteria($criterion, 'entity');
        }

        $qb = $ca->getQueryBuilder();

        foreach ($orderBy as $field => $order) {
            $qb->addOrderBy(sprintf('entity.%s', $field), $order);
        }

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        if (null !== $offset) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }

    /**
     * @param array<CriteriaInterface>    $criteria
     * @param array<string, 'ASC'|'DESC'> $orderBy
     *
     * @return array<T>
     */
    public function findByCriteria(array $criteria, array $orderBy = [], ?int $limit = null, ?int $offset = null): array
    {
        $qb = $this->createQueryBuilderByCriteria($criteria, $orderBy, $limit, $offset);

        /** @var array<T> $entities */
        $entities = $qb->getQuery()->getResult();

        return $entities;
    }

    /**
     * @param array<CriteriaInterface>    $criteria
     * @param array<string, 'ASC'|'DESC'> $orderBy
     *
     * @return ?T
     */
    public function findOneByCriteria(array $criteria, array $orderBy = []): ?object
    {
        $qb = $this->createQueryBuilderByCriteria($criteria, $orderBy);

        /** @var ?T $entity */
        $entity = $qb->getQuery()->getOneOrNullResult();

        return $entity;
    }

    /**
     * @param array<CriteriaInterface> $criteria
     */
    public function countByCriteria(array $criteria): int
    {
        $qb = $this->createQueryBuilderByCriteria($criteria);

        $rootAlias = $qb->getRootAliases()[0] ?? throw new \RuntimeException('No root alias found.');

        return intval($qb->select(sprintf('count(%s.id)', $rootAlias))->getQuery()->getSingleScalarResult());
    }
}
