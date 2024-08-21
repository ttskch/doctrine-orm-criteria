<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Criteria;

use Doctrine\ORM\QueryBuilder;

interface CriteriaInterface
{
    public function apply(QueryBuilder $qb, string $alias): void;
}
