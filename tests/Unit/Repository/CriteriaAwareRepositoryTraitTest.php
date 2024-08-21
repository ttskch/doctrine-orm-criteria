<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Unit\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Ttskch\DoctrineOrmCriteria\Criteria\CriteriaInterface;
use Ttskch\DoctrineOrmCriteria\Repository\CriteriaAwareRepositoryTrait;

class CriteriaAwareRepositoryTraitTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateQueryBuilderByCriteria(): void
    {
        $qb = $this->prophesize(QueryBuilder::class);
        $qb->addOrderBy('entity.field', 'ASC')->shouldBeCalledTimes(1);
        $qb->setMaxResults(10)->shouldBeCalledTimes(1);
        $qb->setFirstResult(20)->shouldBeCalledTimes(1);

        $SUT = new CriteriaAwareRepositoryImpl($qb->reveal());

        $criteria = $this->prophesize(CriteriaInterface::class);
        $criteria->apply($qb, 'entity')->shouldBeCalledTimes(1);

        $actual = $SUT->createQueryBuilderByCriteria([$criteria->reveal()], ['field' => 'ASC'], 10, 20);
        self::assertSame($qb->reveal(), $actual);
    }

    public function testFindByCriteria(): void
    {
        $qb = $this->prophesize(QueryBuilder::class);
        $qb->addOrderBy('entity.field', 'ASC')->shouldBeCalledTimes(1);
        $qb->setMaxResults(10)->shouldBeCalledTimes(1);
        $qb->setFirstResult(20)->shouldBeCalledTimes(1);

        $query = $this->prophesize(Query::class);
        $query->getResult()->willReturn([$object = new \stdClass()]);
        $qb->getQuery()->willReturn($query->reveal());

        $SUT = new CriteriaAwareRepositoryImpl($qb->reveal());

        $criteria = $this->prophesize(CriteriaInterface::class);
        $criteria->apply($qb, 'entity')->shouldBeCalledTimes(1);

        $actual = $SUT->findByCriteria([$criteria->reveal()], ['field' => 'ASC'], 10, 20);
        self::assertSame([$object], $actual);
    }

    public function testFindOneByCriteria(): void
    {
        $qb = $this->prophesize(QueryBuilder::class);
        $qb->addOrderBy('entity.field', 'ASC')->shouldBeCalledTimes(1);

        $query = $this->prophesize(Query::class);
        $query->getOneOrNullResult()->willReturn($object = new \stdClass());
        $qb->getQuery()->willReturn($query->reveal());

        $SUT = new CriteriaAwareRepositoryImpl($qb->reveal());

        $criteria = $this->prophesize(CriteriaInterface::class);
        $criteria->apply($qb, 'entity')->shouldBeCalledTimes(1);

        $actual = $SUT->findOneByCriteria([$criteria->reveal()], ['field' => 'ASC']);
        self::assertSame($object, $actual);
    }

    public function testCountByCriteria(): void
    {
        $qb = $this->prophesize(QueryBuilder::class);
        $qb->getRootAliases()->willReturn(['entity']);

        $query = $this->prophesize(Query::class);
        $query->getSingleScalarResult()->willReturn(10);
        $qb->select('count(entity.id)')->willReturn($qb->reveal());
        $qb->getQuery()->willReturn($query->reveal());

        $SUT = new CriteriaAwareRepositoryImpl($qb->reveal());

        $criteria = $this->prophesize(CriteriaInterface::class);
        $criteria->apply($qb, 'entity')->shouldBeCalledTimes(1);

        $actual = $SUT->countByCriteria([$criteria->reveal()]);
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
