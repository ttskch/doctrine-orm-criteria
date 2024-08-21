<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Unit\Criteria;

use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Ttskch\DoctrineOrmCriteria\Criteria\OrderBy;

class OrderByTest extends TestCase
{
    use ProphecyTrait;

    public function testApply(): void
    {
        $qb = $this->prophesize(QueryBuilder::class);
        $qb->addOrderBy('alias.property', 'ASC')->shouldBeCalledTimes(1);

        $SUT = new OrderBy('property', 'ASC');

        $SUT->apply($qb->reveal(), 'alias');
    }
}
