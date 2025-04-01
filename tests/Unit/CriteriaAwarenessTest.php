<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Unit;

use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Ttskch\DoctrineOrmCriteria\Criteria\CriteriaInterface;
use Ttskch\DoctrineOrmCriteria\CriteriaAwareness;

class CriteriaAwarenessTest extends TestCase
{
    public function testAddCriteria(): void
    {
        $qb = self::createStub(QueryBuilder::class);

        $SUT = new CriteriaAwareness($qb);

        $criteria = $this->createMock(CriteriaInterface::class);
        $criteria->expects($this->once())->method('apply')->with($qb, 'alias');

        $actual = $SUT->addCriteria($criteria, 'alias');
        self::assertSame($SUT, $actual);
    }

    public function testGetQueryBuilder(): void
    {
        $qb = self::createStub(QueryBuilder::class);

        $SUT = new CriteriaAwareness($qb);

        self::assertSame($qb, $SUT->getQueryBuilder());
    }
}
