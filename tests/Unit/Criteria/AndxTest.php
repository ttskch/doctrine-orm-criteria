<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Unit\Criteria;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Ttskch\DoctrineOrmCriteria\Criteria\Andx;
use Ttskch\DoctrineOrmCriteria\Criteria\CriteriaInterface;

use function Lib\Functions\strval;

class AndxTest extends TestCase
{
    public function testCombine(): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('andWhere')->with('where');

        $criteria = static::createStub(CriteriaInterface::class);

        $SUT = new Andx([$criteria]);

        $SUT->combine($qb, 'where');
    }

    public function testApply(): void
    {
        // $qb will be cloned so it cannot be a mock
        ($qb = new QueryBuilder(static::createStub(EntityManagerInterface::class)))
            ->where('where')
        ;

        $criteria = $this->createMock(CriteriaInterface::class);
        $criteria->expects($this->exactly(2))->method('apply')->with(self::callback(function (QueryBuilder $arg1) use ($qb) {
            static $i = 0;

            return match ($i++) { // @phpstan-ignore postInc.type
                0 => $arg1 !== $qb,
                1 => $arg1 === $qb,
                default => throw new \LogicException(),
            };
        }), 'alias');

        $SUT = new Andx([$criteria]);

        $SUT->apply($qb, 'alias');

        self::assertSame('where', strval($qb->getDQLPart('where')));
    }
}
