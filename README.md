# Doctrine ORM Criteria

[![](https://github.com/ttskch/doctrine-orm-criteria/actions/workflows/ci.yaml/badge.svg?branch=main)](https://github.com/ttskch/doctrine-orm-criteria/actions/workflows/ci.yaml?query=branch:main)
[![codecov](https://codecov.io/gh/ttskch/doctrine-orm-criteria/graph/badge.svg?token=gu1GDphBHg)](https://codecov.io/gh/ttskch/doctrine-orm-criteria)
[![Latest Stable Version](https://poser.pugx.org/ttskch/doctrine-orm-criteria/version?format=flat-square)](https://packagist.org/packages/ttskch/doctrine-orm-criteria)
[![Total Downloads](https://poser.pugx.org/ttskch/doctrine-orm-criteria/downloads?format=flat-square)](https://packagist.org/packages/ttskch/doctrine-orm-criteria/stats)

## Introduction

[`QueryBuilder`](https://www.doctrine-project.org/projects/doctrine-orm/en/2.20/reference/query-builder.html) of [doctrine/orm](https://www.doctrine-project.org/projects/doctrine-orm/en/2.20/index.html) has a method called [`addCriteria()`](https://www.doctrine-project.org/projects/doctrine-orm/en/2.20/reference/query-builder.html#adding-a-criteria-to-a-query) that allows you to build queries by combining [`Criteria`](https://www.doctrine-project.org/projects/doctrine-orm/en/2.20/reference/working-with-associations.html#filtering-collections) of [doctrine/collections](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html). This allows you to separate the concerns of "search conditions" into a `Criteria`, improving the maintainability of your codebase.

However, `Criteria` of doctrine/collections only has a very limited matching language because it is designed to work both on the SQL and the PHP collection level, and therefore cannot be used to build complex queries.

Rejoice! **Doctrine ORM Criteria** allows you to separate any complex "search condition" as a `Criteria` with a specialized API for `QueryBuilder` of doctrine/orm just like below.

```php
$qb = (new CriteriaAwareness($fooRepository->createQueryBuilder('f')))
    ->addCriteria(new IsPublic(), 'f')
    ->addCriteria(new IsAccessibleBy($user), 'f')
    ->addCriteria(new CategoryIs($category), 'f')
    ->addCriteria(new OrderByRandom(), 'f')
    ->getQueryBuilder()
;
```

```php
final readonly class IsPublic implements CriteriaInterface
{
    public ?\DateTimeInterface $at;

    public function __construct(?\DateTimeInterface $at = null)
    {
        $this->at = $at ?? new \DateTimeImmutable();
    }

    public function apply(QueryBuilder $qb, string $alias): void
    {
        $qb
            ->andWhere("$alias.state = :state")
            ->andWhere($qb->expr()->andX(
                $qb->expr()->orX(
                    "$alias.openedAt IS NULL",
                    "$alias.openedAt <= :at",
                ),
                $qb->expr()->orX(
                    "$alias.closedAt IS NULL",
                    "$alias.closedAt > :at",
                ),
            ))
            ->setParameter('state', Foo::STATE_PUBLIC)
            ->setParameter('at', $this->at)
        ;
    }
}
```

## Requirements

* PHP: ^8.0
* Doctrine ORM: ^2.8

> Support for Doctrine ORM v3 is coming soon.

## Installation

```shell
$ composer require ttskch/doctrine-orm-criteria
```

## Usage

### Basic

You can create your own `Criteria` by implementing [`CriteriaInterface`](src/Criteria/CriteriaInterface.php) and adding it to [`CriteriaAwareness`](src/CriteriaAwareness.php) to build queries.

```php
use App\Repository\Criteria\Foo\IsPublic;
use Ttskch\DoctrineOrmCriteria\CriteriaAwareness;

$qb = (new CriteriaAwareness($fooRepository->createQueryBuilder('f')))
    ->addCriteria(new IsPublic(), 'f')
    ->getQueryBuilder()
;
```

```php
<?php

declare(strict_types=1);

namespace App\Repository\Criteria\Foo;

final readonly class IsPublic implements CriteriaInterface
{
    public ?\DateTimeInterface $at;

    public function __construct(?\DateTimeInterface $at = null)
    {
        $this->at = $at ?? new \DateTimeImmutable();
    }

    public function apply(QueryBuilder $qb, string $alias): void
    {
        $qb
            ->andWhere("$alias.state = :state")
            ->andWhere($qb->expr()->andX(
                $qb->expr()->orX(
                    "$alias.openedAt IS NULL",
                    "$alias.openedAt <= :at",
                ),
                $qb->expr()->orX(
                    "$alias.closedAt IS NULL",
                    "$alias.closedAt > :at",
                ),
            ))
            ->setParameter('state', Foo::STATE_PUBLIC)
            ->setParameter('at', $this->at)
        ;
    }
}
```

### Built-in Criteria and utilities

There are some built-in `Criteria`: `OrderBy`, `Andx`, and `Orx`. Using `Andx` and `Orx`, you can combine multiple `Criteria` to create a new `Criteria`.

```php
<?php

declare(strict_types=1);

namespace App\Repository\Criteria\Foo;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Ttskch\DoctrineOrmCriteria\Criteria\CriteriaInterface;
use Ttskch\DoctrineOrmCriteria\Criteria\Andx;
use Ttskch\DoctrineOrmCriteria\Criteria\Orx;

final readonly class IsViewable implements CriteriaInterface
{
    public ?\DateTimeInterface $at;

    public function __construct(
        public User $me,
        ?\DateTimeInterface $at = null,
    ) {
        $this->at = $at ?? new \DateTimeImmutable();
    }

    public function apply(QueryBuilder $qb, string $alias): void
    {
        (new Andx([
            new Orx([
                new IsPublic($this->at),
                ...array_map(fn (string $category) => new CategoryIs($category), Foo::PUBLIC_CATEGORIES),
            ])
            new IsAccessibleBy($this->me),
        ]))->apply($qb, $alias);
    }
}
```

Additionally, when creating your own `Criteria`, you can use [`AddSelectTrait`](src/Criteria/Traits/AddSelectTrait.php) and [`JoinTrait`](src/Criteria/Traits/JoinTrait.php) to ensure that the `addSelect()` and `join` are IDEMPOTENT even if the `Criteria` is applied multiple times to the `QueryBuilder`.

```php
<?php

declare(strict_types=1);

namespace App\Repository\Criteria\Foo;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Ttskch\DoctrineOrmCriteria\Criteria\CriteriaInterface;
use Ttskch\DoctrineOrmCriteria\Criteria\Traits\JoinTrait;

final readonly class IsAccessibleBy implements CriteriaInterface
{
    use JoinTrait;

    private const string CRITERIA_KEY = 'Foo_IsAccessibleBy'; // some unique key

    public function __construct(public User $me)
    {
    }

    public function apply(QueryBuilder $qb, string $alias): void
    {
        $userAlias = sprintf('%s_%s_user', self::CRITERIA_KEY, $alias);

        $this->leftJoin($qb, sprintf('%s.user', $alias), $userAlias);

        $qb
            ->andWhere(sprintf('%s = :user', $userAlias))
            ->setParameter('user', $this->me)
        ;
    }
}
```

### Integration with Repository

You can also easily integrate with your repositories using [`CriteriaAwareRepositoryTrait`](src/Repository/CriteriaAwareRepositoryTrait.php).

```diff
  <?php

  declare(strict_types=1);

  namespace App\Repository;

  use App\Entity\Foo;
  use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
  use Doctrine\Persistence\ManagerRegistry;
+ use Ttskch\DoctrineOrmCriteria\Repository\CriteriaAwareRepositoryTrait;

  /**
   * @extends ServiceEntityRepository<Foo>
   */
  class FooRepository extends ServiceEntityRepository
  {
+     /** @use CriteriaAwareRepositoryTrait<Foo> */
+     use CriteriaAwareRepositoryTrait;
+
      public function __construct(ManagerRegistry $registry)
      {
          parent::__construct($registry, Foo::class);
      }
  }
```

```php
$foos = $fooRepository->createQueryBuilder('f')->findByCriteria([
    new IsPublic(),
    new IsAccessibleBy($user),
    new CategoryIs($category),
    new OrderByRandom(),
]);

\PHPStan\dumpType($foos); // Dumped type: array<App\Entity\Foo>

$foo = $fooRepository->createQueryBuilder('f')->findOneByCriteria([
    new IsPublic(),
    new IsAccessibleBy($user),
    new CategoryIs($category),
    new OrderByRandom(),
]);

\PHPStan\dumpType($foo); // Dumped type: App\Entity\Foo

$count = $fooRepository->createQueryBuilder('f')->countByCriteria([
    new IsPublic(),
    new IsAccessibleBy($user),
    new CategoryIs($category),
]);

\PHPStan\dumpType($count); // Dumped type: int
```

## Getting involved

```shell
$ composer install
$ composer bin all install

# Develop...

$ composer tests
```
