<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria\Criteria\Traits;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

trait AddSelectTrait
{
    public function addSelect(QueryBuilder $qb, string $select, ?string $alias = null, ?bool $hidden = false): void
    {
        if (!$this->isAlreadyAddedToSelect($qb, $select, $alias)) {
            if (null !== $alias) {
                $select .= sprintf(' as %s%s', boolval($hidden) ? 'hidden ' : '', $alias);
            }
            $qb->addSelect($select);
        }
    }

    private function isAlreadyAddedToSelect(QueryBuilder $qb, string $select, ?string $alias): bool
    {
        $selectDqlPart = $qb->getDQLPart('select');
        assert(is_array($selectDqlPart));

        foreach ($selectDqlPart as $selectExpr) {
            assert($selectExpr instanceof Expr\Select);
            foreach ($selectExpr->getParts() as $part) {
                if (is_string($part) && (str_contains($part, $select) || (null !== $alias && str_contains($part, $alias)))) {
                    return true;
                }
            }
        }

        return false;
    }
}
