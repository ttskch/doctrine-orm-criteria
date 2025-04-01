<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Unit\Criteria;

use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Ttskch\DoctrineOrmCriteria\Criteria\OrderBy;

class OrderByTest extends TestCase
{
    public function testApply(): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('addOrderBy')->with('alias.property', 'ASC');

        $SUT = new OrderBy('property', 'ASC');

        $SUT->apply($qb, 'alias');
    }
}
