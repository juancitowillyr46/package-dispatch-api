<?php

declare(strict_types=1);

namespace App\Dispatch\Domain\Entity;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final class DispatchHistory
{
    private string $id;
    private Dispatch $dispatch;
    private ?string $previousStatus;
    private string $newStatus;
    private DateTimeImmutable $changedAt;

    public function __construct(Dispatch $dispatch, ?string $previousStatus, string $newStatus, ?DateTimeImmutable $changedAt = null)
    {
        $this->id = Uuid::v7()->toRfc4122();
        $this->dispatch = $dispatch;
        $this->previousStatus = $previousStatus;
        $this->newStatus = $newStatus;
        $this->changedAt = $changedAt ?? new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDispatch(): Dispatch
    {
        return $this->dispatch;
    }

    public function getPreviousStatus(): ?string
    {
        return $this->previousStatus;
    }

    public function getNewStatus(): string
    {
        return $this->newStatus;
    }

    public function getChangedAt(): DateTimeImmutable
    {
        return $this->changedAt;
    }
}
