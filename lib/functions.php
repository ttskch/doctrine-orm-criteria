<?php

declare(strict_types=1);

namespace Lib\Functions;

use Ttskch\DoctrineOrmCriteria\Exception\LogicException;

if (!\function_exists(strval::class)) { // @phpstan-ignore class.notFound
    /**
     * @see https://blog.ichikaway.com/entry/2023/07/15/phpstan-intval
     * @see https://github.com/phpstan/phpstan/issues/9295
     */
    function strval(mixed $value): string
    {
        if ($value instanceof \Stringable) {
            return $value->__toString();
        }

        if (!is_bool($value) && !is_float($value) && !is_int($value) && !is_resource($value) && !is_string($value) && !is_null($value)) {
            throw new LogicException();
        }

        return \strval($value);
    }
}
