<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria;

use Doctrine\ORM\QueryBuilder;
use Ttskch\DoctrineOrmCriteria\Criteria\CriteriaInterface;

interface CriteriaAwarenessInterface
{
    public function addCriteria(CriteriaInterface $criteria, string $alias): self;

    public function getQueryBuilder(): QueryBuilder;
}
