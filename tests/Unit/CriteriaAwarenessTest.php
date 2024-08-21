<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Unit;

use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Ttskch\DoctrineOrmCriteria\Criteria\CriteriaInterface;
use Ttskch\DoctrineOrmCriteria\CriteriaAwareness;

class CriteriaAwarenessTest extends TestCase
{
    use ProphecyTrait;

    public function testAddCriteria(): void
    {
        $qb = $this->prophesize(QueryBuilder::class);

        $SUT = new CriteriaAwareness($qb->reveal());

        $criteria = $this->prophesize(CriteriaInterface::class);
        $criteria->apply($qb, 'alias')->shouldBeCalledTimes(1);

        $actual = $SUT->addCriteria($criteria->reveal(), 'alias');
        self::assertSame($SUT, $actual);
    }

    public function testGetQueryBuilder(): void
    {
        $qb = $this->prophesize(QueryBuilder::class);

        $SUT = new CriteriaAwareness($qb->reveal());

        self::assertSame($qb->reveal(), $SUT->getQueryBuilder());
    }
}
