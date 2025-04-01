<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Unit\Criteria\Traits;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ttskch\DoctrineOrmCriteria\Criteria\Traits\AddSelectTrait;

class AddSelectTraitTest extends TestCase
{
    /**
     * @dataProvider addSelectDataProvider
     */
    #[DataProvider('addSelectDataProvider')]
    public function testAddSelect(?string $existentSelect, string $select, ?string $alias, bool $hidden, ?string $addedSelect): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $qb->method('getDQLPart')->with('select')->willReturn($existentSelect !== null ? [new Expr\Select([$existentSelect])] : []);
        if ($addedSelect !== null) {
            $qb->expects($this->once())->method('addSelect')->with($addedSelect);
        } else {
            $qb->expects($this->never())->method('addSelect');
        }

        $SUT = new AddSelectImpl();

        $SUT->addSelect($qb, $select, $alias, $hidden);
    }

    /**
     * @return array<mixed>
     */
    public static function addSelectDataProvider(): array
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
