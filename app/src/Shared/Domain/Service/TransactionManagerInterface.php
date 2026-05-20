<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service;

interface TransactionManagerInterface
{
    /**
     * @template T
     *
     * @param callable():T $callback
     *
     * @return T
     */
    public function run(callable $callback): mixed;
}
