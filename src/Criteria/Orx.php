<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Criteria;

use Doctrine\ORM\QueryBuilder;

final class Orx extends Composite implements CriteriaInterface
{
    public function combine(QueryBuilder $qb, mixed $where): void
    {
        $qb->orWhere($where);
    }
}
