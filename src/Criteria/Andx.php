<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Criteria;

use Doctrine\ORM\QueryBuilder;

final class Andx extends Composite implements CriteriaInterface
{
    public function combine(QueryBuilder $qb, mixed $where): void
    {
        $qb->andWhere($where);
    }
}
