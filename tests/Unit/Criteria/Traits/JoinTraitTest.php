<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Unit\Criteria\Traits;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ttskch\DoctrineOrmCriteria\Criteria\Traits\JoinTrait;

class JoinTraitTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param Expr\Join::ON|Expr\Join::WITH|null $conditionType
     */
    #[DataProvider('dataProvider')]
    public function testJoin(?string $existentAlias, string $join, string $alias, ?string $conditionType, ?string $condition, ?string $indexBy, bool $addSelect, ?string $addedJoin, ?string $addedSelect): void
    {
        $qb = $this->createMock(QueryBuilder::class);

        if ($addedJoin !== null) {
            $qb->expects($this->once())->method('join')->with($addedJoin, $alias, $conditionType, $condition, $indexBy);
        } else {
            $qb->expects($this->never())->method('join');
        }

        $getDQLPartJoinReturn = $existentAlias !== null ? [[new Expr\Join('LEFT', '', $existentAlias)]] : [];

        if ($addedSelect !== null) {
            $qb->method('getDQLPart')
                ->with(self::callback(function (string $arg1) {
                    static $i = 0;

                    return match ($i++) { // @phpstan-ignore postInc.type
                        0 => $arg1 === 'join',
                        1 => $arg1 === 'select',
                        default => throw new \LogicException(),
                    };
                }))
                ->willReturn($getDQLPartJoinReturn, [])
            ;
            $qb->expects($this->once())->method('addSelect')->with($addedSelect);
        } else {
            $qb->method('getDQLPart')->with('join')->willReturn($getDQLPartJoinReturn);
            $qb->expects($this->never())->method('addSelect');
        }

        $SUT = new JoinTraitImpl();

        $SUT->join($qb, $join, $alias, $conditionType, $condition, $indexBy, $addSelect);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param Expr\Join::ON|Expr\Join::WITH|null $conditionType
     */
    #[DataProvider('dataProvider')]
    public function testInnerJoin(?string $existentAlias, string $join, string $alias, ?string $conditionType, ?string $condition, ?string $indexBy, bool $addSelect, ?string $addedJoin, ?string $addedSelect): void
    {
        $qb = $this->createMock(QueryBuilder::class);

        if ($addedJoin !== null) {
            $qb->expects($this->once())->method('innerJoin')->with($addedJoin, $alias, $conditionType, $condition, $indexBy);
        } else {
            $qb->expects($this->never())->method('innerJoin');
        }

        $getDQLPartJoinReturn = $existentAlias !== null ? [[new Expr\Join('LEFT', '', $existentAlias)]] : [];

        if ($addedSelect !== null) {
            $qb->method('getDQLPart')
                ->with(self::callback(function (string $arg1) {
                    static $i = 0;

                    return match ($i++) { // @phpstan-ignore postInc.type
                        0 => $arg1 === 'join',
                        1 => $arg1 === 'select',
                        default => throw new \LogicException(),
                    };
                }))
                ->willReturn($getDQLPartJoinReturn, [])
            ;
            $qb->expects($this->once())->method('addSelect')->with($addedSelect);
        } else {
            $qb->method('getDQLPart')->with('join')->willReturn($getDQLPartJoinReturn);
            $qb->expects($this->never())->method('addSelect');
        }

        $SUT = new JoinTraitImpl();

        $SUT->innerJoin($qb, $join, $alias, $conditionType, $condition, $indexBy, $addSelect);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param Expr\Join::ON|Expr\Join::WITH|null $conditionType
     */
    #[DataProvider('dataProvider')]
    public function testLeftJoin(?string $existentAlias, string $join, string $alias, ?string $conditionType, ?string $condition, ?string $indexBy, bool $addSelect, ?string $addedJoin, ?string $addedSelect): void
    {
        $qb = $this->createMock(QueryBuilder::class);

        if ($addedJoin !== null) {
            $qb->expects($this->once())->method('leftJoin')->with($addedJoin, $alias, $conditionType, $condition, $indexBy);
        } else {
            $qb->expects($this->never())->method('leftJoin');
        }

        $getDQLPartJoinReturn = $existentAlias !== null ? [[new Expr\Join('LEFT', '', $existentAlias)]] : [];

        if ($addedSelect !== null) {
            $qb->method('getDQLPart')
                ->with(self::callback(function (string $arg1) {
                    static $i = 0;

                    return match ($i++) { // @phpstan-ignore postInc.type
                        0 => $arg1 === 'join',
                        1 => $arg1 === 'select',
                        default => throw new \LogicException(),
                    };
                }))
                ->willReturn($getDQLPartJoinReturn, [])
            ;
            $qb->expects($this->once())->method('addSelect')->with($addedSelect);
        } else {
            $qb->method('getDQLPart')->with('join')->willReturn($getDQLPartJoinReturn);
            $qb->expects($this->never())->method('addSelect');
        }

        $SUT = new JoinTraitImpl();

        $SUT->leftJoin($qb, $join, $alias, $conditionType, $condition, $indexBy, $addSelect);
    }

    /**
     * @return array<mixed>
     */
    public static function dataProvider(): array
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
