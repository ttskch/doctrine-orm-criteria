<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Criteria\Traits;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

trait JoinTrait
{
    use AddSelectTrait;

    /**
     * @param Expr\Join::ON|Expr\Join::WITH|null $conditionType
     *
     * @see QueryBuilder::join()
     */
    public function join(QueryBuilder $qb, string $join, string $alias, ?string $conditionType = null, string|Expr\Comparison|Expr\Composite|Expr\Func|null $condition = null, ?string $indexBy = null, bool $addSelect = false): void
    {
        if (!$this->isAlreadyJoined($qb, $alias)) {
            $qb->join($join, $alias, $conditionType, $condition, $indexBy);
            if ($addSelect) {
                $this->addSelect($qb, $alias);
            }
        }
    }

    /**
     * @param Expr\Join::ON|Expr\Join::WITH|null $conditionType
     *
     * @see QueryBuilder::innerJoin()
     */
    public function innerJoin(QueryBuilder $qb, string $join, string $alias, ?string $conditionType = null, string|Expr\Comparison|Expr\Composite|Expr\Func|null $condition = null, ?string $indexBy = null, bool $addSelect = false): void
    {
        if (!$this->isAlreadyJoined($qb, $alias)) {
            $qb->innerJoin($join, $alias, $conditionType, $condition, $indexBy);
            if ($addSelect) {
                $qb->addSelect($alias);
            }
        }
    }

    /**
     * @param Expr\Join::ON|Expr\Join::WITH|null $conditionType
     *
     * @see QueryBuilder::leftJoin()
     */
    public function leftJoin(QueryBuilder $qb, string $join, string $alias, ?string $conditionType = null, string|Expr\Comparison|Expr\Composite|Expr\Func|null $condition = null, ?string $indexBy = null, bool $addSelect = false): void
    {
        if (!$this->isAlreadyJoined($qb, $alias)) {
            $qb->leftJoin($join, $alias, $conditionType, $condition, $indexBy);
            if ($addSelect) {
                $qb->addSelect($alias);
            }
        }
    }

    private function isAlreadyJoined(QueryBuilder $qb, string $alias): bool
    {
        $joinDqlPart = $qb->getDQLPart('join');
        assert(is_array($joinDqlPart));

        foreach ($joinDqlPart as $joins) {
            foreach ($joins as $joinExpr) {
                assert($joinExpr instanceof Expr\Join);
                if ($joinExpr->getAlias() === $alias) {
                    return true;
                }
            }
        }

        return false;
    }
}
