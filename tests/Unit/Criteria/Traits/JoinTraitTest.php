<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Unit\Criteria\Traits;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Ttskch\DoctrineOrmCriteria\Criteria\Traits\JoinTrait;

class JoinTraitTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @dataProvider dataProvider
     *
     * @param Expr\Join::ON|Expr\Join::WITH|null $conditionType
     */
    public function testJoin(?string $existentAlias, string $join, string $alias, ?string $conditionType, ?string $condition, ?string $indexBy, bool $addSelect, ?string $addedJoin, ?string $addedSelect): void
    {
        $qb = $this->prophesize(QueryBuilder::class);
        $qb->getDQLPart('join')->willReturn($existentAlias !== null ? [[new Expr\Join('LEFT', '', $existentAlias)]] : []);

        if ($addedJoin !== null) {
            $qb->join($addedJoin, $alias, $conditionType, $condition, $indexBy)->shouldBeCalledTimes(1);
        } else {
            $qb->join(Argument::cetera())->shouldNotBeCalled();
        }

        if ($addedSelect !== null) {
            $qb->getDQLPart('select')->willReturn([]);
            $qb->addSelect($addedSelect)->shouldBeCalledTimes(1);
        } else {
            $qb->addSelect(Argument::cetera())->shouldNotBeCalled();
        }

        $SUT = new JoinTraitImpl();

        $SUT->join($qb->reveal(), $join, $alias, $conditionType, $condition, $indexBy, $addSelect);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param Expr\Join::ON|Expr\Join::WITH|null $conditionType
     */
    public function testInnerJoin(?string $existentAlias, string $join, string $alias, ?string $conditionType, ?string $condition, ?string $indexBy, bool $addSelect, ?string $addedJoin, ?string $addedSelect): void
    {
        $qb = $this->prophesize(QueryBuilder::class);
        $qb->getDQLPart('join')->willReturn($existentAlias !== null ? [[new Expr\Join('LEFT', '', $existentAlias)]] : []);

        if ($addedJoin !== null) {
            $qb->innerJoin($addedJoin, $alias, $conditionType, $condition, $indexBy)->shouldBeCalledTimes(1);
        } else {
            $qb->innerJoin(Argument::cetera())->shouldNotBeCalled();
        }

        if ($addedSelect !== null) {
            $qb->getDQLPart('select')->willReturn([]);
            $qb->addSelect($addedSelect)->shouldBeCalledTimes(1);
        } else {
            $qb->addSelect(Argument::cetera())->shouldNotBeCalled();
        }

        $SUT = new JoinTraitImpl();

        $SUT->innerJoin($qb->reveal(), $join, $alias, $conditionType, $condition, $indexBy, $addSelect);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param Expr\Join::ON|Expr\Join::WITH|null $conditionType
     */
    public function testLeftJoin(?string $existentAlias, string $join, string $alias, ?string $conditionType, ?string $condition, ?string $indexBy, bool $addSelect, ?string $addedJoin, ?string $addedSelect): void
    {
        $qb = $this->prophesize(QueryBuilder::class);
        $qb->getDQLPart('join')->willReturn($existentAlias !== null ? [[new Expr\Join('LEFT', '', $existentAlias)]] : []);

        if ($addedJoin !== null) {
            $qb->leftJoin($addedJoin, $alias, $conditionType, $condition, $indexBy)->shouldBeCalledTimes(1);
        } else {
            $qb->leftJoin(Argument::cetera())->shouldNotBeCalled();
        }

        if ($addedSelect !== null) {
            $qb->getDQLPart('select')->willReturn([]);
            $qb->addSelect($addedSelect)->shouldBeCalledTimes(1);
        } else {
            $qb->addSelect(Argument::cetera())->shouldNotBeCalled();
        }

        $SUT = new JoinTraitImpl();

        $SUT->leftJoin($qb->reveal(), $join, $alias, $conditionType, $condition, $indexBy, $addSelect);
    }

    /**
     * @return array<mixed>
     */
    public function dataProvider(): array
    {
        return [
            [null, 'entity.field', 'alias', null, null, null, false, 'entity.field', null],
            [null, 'entity.field', 'alias', null, null, null, true, 'entity.field', 'alias'],
            ['foo', 'entity.field', 'alias', null, null, null, false, 'entity.field', null],
            ['foo', 'entity.field', 'alias', null, null, null, true, 'entity.field', 'alias'],
            ['alias', 'entity.field', 'alias', null, null, null, false, null, null],
            ['alias', 'entity.field', 'alias', null, null, null, true, null, null], // If already joined, select is also not added
        ];
    }
}

class JoinTraitImpl
{
    use JoinTrait;
}
