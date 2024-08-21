<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Unit\Criteria\Traits;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Ttskch\DoctrineOrmCriteria\Criteria\Traits\AddSelectTrait;

class AddSelectTraitTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @dataProvider addSelectDataProvider
     */
    public function testAddSelect(?string $existentSelect, string $select, ?string $alias, bool $hidden, ?string $addedSelect): void
    {
        $qb = $this->prophesize(QueryBuilder::class);
        $qb->getDQLPart('select')->willReturn($existentSelect !== null ? [new Expr\Select([$existentSelect])] : []);
        if ($addedSelect !== null) {
            $qb->addSelect($addedSelect)->shouldBeCalledTimes(1);
        } else {
            $qb->addSelect(Argument::cetera())->shouldNotBeCalled();
        }

        $SUT = new AddSelectImpl();

        $SUT->addSelect($qb->reveal(), $select, $alias, $hidden);
    }

    /**
     * @return array<mixed>
     */
    public function addSelectDataProvider(): array
    {
        return [
            [null, 'entity.field', null, false, 'entity.field'],
            ['foo.bar', 'entity.field', null, false, 'entity.field'],
            ['entity.field', 'entity.field', null, false, null],
            ['entity.field as alias', 'foo.bar', 'alias', false, null],
            ['entity.field as alias', 'foo.bar', 'baz', false, 'foo.bar as baz'],
            ['entity.field as alias', 'foo.bar', 'baz', true, 'foo.bar as hidden baz'],
        ];
    }
}

class AddSelectImpl
{
    use AddSelectTrait;
}
