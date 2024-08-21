<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Criteria;

use Doctrine\ORM\QueryBuilder;

abstract class Composite implements CriteriaInterface
{
    public function __construct(
        /**
         * @readonly
         *
         * @var array<CriteriaInterface>
         */
        public array $criteria,
    ) {
    }

    public function apply(QueryBuilder $qb, string $alias): void
    {
        foreach ($this->criteria as $criterion) {
            /** @psalm-suppress MixedAssignment */
            $originalWhere = $qb->getDQLPart('where');

            // Gets the WHERE clause that would be generated if $criterion were applied alone
            $qb2 = (clone $qb)->resetDQLPart('where');
            $criterion->apply($qb2, $alias);
            /** @psalm-suppress MixedAssignment */
            $where = $qb2->getDQLPart('where');

            // Actually apply $criterion to reflect JOIN etc., then delete only the WHERE clause
            $criterion->apply($qb, $alias);
            $qb->resetDQLPart('where');

            // Combine the original WHERE clause with the WHERE clause applied with $criterion
            $qb->where($originalWhere);
            $this->combine($qb, $where);
        }
    }

    abstract public function combine(QueryBuilder $qb, mixed $where): void;
}
