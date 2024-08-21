<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Criteria;

use Doctrine\ORM\QueryBuilder;

final class OrderBy implements CriteriaInterface
{
    public function __construct(
        /**
         * @readonly
         */
        public string $property,
        /**
         * @readonly
         *
         * @var 'ASC'|'DESC'
         */
        public string $direction = 'DESC',
    ) {
    }

    public function apply(QueryBuilder $qb, string $alias): void
    {
        $qb->addOrderBy(sprintf('%s.%s', $alias, $this->property), $this->direction);
    }
}
