<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria;

use Doctrine\ORM\QueryBuilder;
use Ttskch\DoctrineOrmCriteria\Criteria\CriteriaInterface;

final class CriteriaAwareness implements CriteriaAwarenessInterface
{
    public function __construct(
        /**
         * @readonly
         */
        private QueryBuilder $qb,
    ) {
    }

    public function addCriteria(CriteriaInterface $criteria, string $alias): self
    {
        $criteria->apply($this->qb, $alias);

        return $this;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->qb;
    }
}
