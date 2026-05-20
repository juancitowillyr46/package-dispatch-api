<?php

declare(strict_types=1);

namespace App\Shared\Application\DTO;

final class PaginatedResult
{
    /**
     * @param array<int, mixed> $items
     */
    public function __construct(
        private readonly array $items,
        private readonly int $page,
        private readonly int $perPage,
        private readonly int $total,
    ) {
    }

    /**
     * @return array<int, mixed>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getTotalPages(): int
    {
        if ($this->perPage <= 0) {
            return 0;
        }

        return (int) ceil($this->total / $this->perPage);
    }
}
