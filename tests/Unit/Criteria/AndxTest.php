<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Unit\Criteria;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Ttskch\DoctrineOrmCriteria\Criteria\Andx;
use Ttskch\DoctrineOrmCriteria\Criteria\CriteriaInterface;

use function Lib\Functions\strval;

class AndxTest extends TestCase
{
    use ProphecyTrait;

    public function testCombine(): void
    {
        $qb = $this->prophesize(QueryBuilder::class);
        $qb->andWhere('where')->shouldBeCalledTimes(1);

        $criteria = $this->prophesize(CriteriaInterface::class);

        $SUT = new Andx([$criteria->reveal()]);

        $SUT->combine($qb->reveal(), 'where');
    }

    public function testApply(): void
    {
        // $qb will be cloned so it cannot be a mock
        ($qb = new QueryBuilder($this->prophesize(EntityManagerInterface::class)->reveal()))
            ->where('where')
        ;

        $criteria = $this->prophesize(CriteriaInterface::class);
        $criteria->apply(Argument::that(fn (QueryBuilder $qb2) => $qb2 !== $qb), 'alias')->shouldBeCalledTimes(1);
        $criteria->apply($qb, 'alias')->shouldBeCalledTimes(1);

        $SUT = new Andx([$criteria->reveal()]);

        $SUT->apply($qb, 'alias');

        self::assertSame('where', strval($qb->getDQLPart('where')));
    }
}
