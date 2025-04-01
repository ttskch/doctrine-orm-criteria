<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Unit\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Ttskch\DoctrineOrmCriteria\Criteria\CriteriaInterface;
use Ttskch\DoctrineOrmCriteria\Repository\CriteriaAwareRepositoryTrait;

class CriteriaAwareRepositoryTraitTest extends TestCase
{
    public function testCreateQueryBuilderByCriteria(): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('addOrderBy')->with('entity.field', 'ASC');
        $qb->expects($this->once())->method('setMaxResults')->with(10);
        $qb->expects($this->once())->method('setFirstResult')->with(20);

        $SUT = new CriteriaAwareRepositoryImpl($qb);

        $criteria = $this->createMock(CriteriaInterface::class);
        $criteria->expects($this->once())->method('apply')->with($qb, 'entity');

        $actual = $SUT->createQueryBuilderByCriteria([$criteria], ['field' => 'ASC'], 10, 20);
        self::assertSame($qb, $actual);
    }

    public function testFindByCriteria(): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('addOrderBy')->with('entity.field', 'ASC');
        $qb->expects($this->once())->method('setMaxResults')->with(10);
        $qb->expects($this->once())->method('setFirstResult')->with(20);

        $query = self::createStub(Query::class);
        $query->method('getResult')->willReturn([$object = new \stdClass()]);
        $qb->method('getQuery')->willReturn($query);

        $SUT = new CriteriaAwareRepositoryImpl($qb);

        $criteria = $this->createMock(CriteriaInterface::class);
        $criteria->expects($this->once())->method('apply')->with($qb, 'entity');

        $actual = $SUT->findByCriteria([$criteria], ['field' => 'ASC'], 10, 20);
        self::assertSame([$object], $actual);
    }

    public function testFindOneByCriteria(): void
    {
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('addOrderBy')->with('entity.field', 'ASC');

        $query = self::createStub(Query::class);
        $query->method('getOneOrNullResult')->willReturn($object = new \stdClass());
        $qb->method('getQuery')->willReturn($query);

        $SUT = new CriteriaAwareRepositoryImpl($qb);

        $criteria = $this->createMock(CriteriaInterface::class);
        $criteria->expects($this->once())->method('apply')->with($qb, 'entity');

        $actual = $SUT->findOneByCriteria([$criteria], ['field' => 'ASC']);
        self::assertSame($object, $actual);
    }

    public function testCountByCriteria(): void
    {
        $qb = self::createStub(QueryBuilder::class);
        $qb->method('getRootAliases')->willReturn(['entity']);

        $query = self::createStub(Query::class);
        $query->method('getSingleScalarResult')->willReturn(10);
        $qb->method('select')->with('count(entity.id)')->willReturn($qb);
        $qb->method('getQuery')->willReturn($query);

        $SUT = new CriteriaAwareRepositoryImpl($qb);

        $criteria = $this->createMock(CriteriaInterface::class);
        $criteria->expects($this->once())->method('apply')->with($qb, 'entity');

        $actual = $SUT->countByCriteria([$criteria]);
        self::assertSame(10, $actual);
    }
}

class CriteriaAwareRepositoryImpl
{
    /** @use CriteriaAwareRepositoryTrait<object> */
    use CriteriaAwareRepositoryTrait;

    public function __construct(
        private QueryBuilder $qb,
    ) {
    }

    public function createQueryBuilder(string $alias, ?string $indexBy = null): QueryBuilder
    {
        return $this->qb;
    }
}
