<?php

declare(strict_types=1);

namespace Ttskch\DoctrineOrmCriteria;

use PHPUnit\Framework\TestCase;

class DoctrineOrmCriteriaTest extends TestCase
{
    protected DoctrineOrmCriteria $doctrineOrmCriteria;

    protected function setUp(): void
    {
        $this->doctrineOrmCriteria = new DoctrineOrmCriteria();
    }

    public function testIsInstanceOfDoctrineOrmCriteria(): void
    {
        $actual = $this->doctrineOrmCriteria;
        self::assertInstanceOf(DoctrineOrmCriteria::class, $actual);
    }
}
