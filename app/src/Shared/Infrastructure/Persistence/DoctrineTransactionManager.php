<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence;

use App\Shared\Domain\Service\TransactionManagerInterface;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTransactionManager implements TransactionManagerInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function run(callable $callback): mixed
    {
        return $this->entityManager->wrapInTransaction($callback);
    }
}
