<?php

declare(strict_types=1);

namespace App\Shared\Application\DTO;

final class PaginationQuery
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 15,
    ) {
    }
}
