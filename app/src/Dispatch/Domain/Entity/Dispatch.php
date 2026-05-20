<?php

declare(strict_types=1);

namespace App\Dispatch\Domain\Entity;

use App\Courier\Domain\Entity\Courier;
use App\Dispatch\Domain\Enum\DispatchStatus;
use App\Package\Domain\Entity\ShipmentPackage;
use App\Shared\Domain\Exception\DomainException;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final class Dispatch
{
    private string $id;
    private ShipmentPackage $package;
    private ?Courier $courier = null;
    private string $status;
    private string $referenceNumber;
    private string $originAddress;
    private string $destinationAddress;
    private ?string $notes;
    private ?DateTimeImmutable $assignedAt = null;
    private ?DateTimeImmutable $pickedUpAt = null;
    private ?DateTimeImmutable $deliveredAt = null;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        ShipmentPackage $package,
        string $referenceNumber,
        string $originAddress,
        string $destinationAddress,
        ?string $notes = null,
    ) {
        $this->id = Uuid::v7()->toRfc4122();
        $this->package = $package;
        $this->referenceNumber = $referenceNumber;
        $this->originAddress = $originAddress;
        $this->destinationAddress = $destinationAddress;
        $this->notes = $notes;
        $this->status = DispatchStatus::Pending->value;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPackage(): ShipmentPackage
    {
        return $this->package;
    }

    public function getCourier(): ?Courier
    {
        return $this->courier;
    }

    public function getCourierId(): ?string
    {
        return $this->courier?->getId();
    }

    public function getStatus(): DispatchStatus
    {
        return DispatchStatus::from($this->status);
    }

    public function getReferenceNumber(): string
    {
        return $this->referenceNumber;
    }

    public function getOriginAddress(): string
    {
        return $this->originAddress;
    }

    public function getDestinationAddress(): string
    {
        return $this->destinationAddress;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getAssignedAt(): ?DateTimeImmutable
    {
        return $this->assignedAt;
    }

    public function getPickedUpAt(): ?DateTimeImmutable
    {
        return $this->pickedUpAt;
    }

    public function getDeliveredAt(): ?DateTimeImmutable
    {
        return $this->deliveredAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function assignCourier(Courier $courier, DateTimeImmutable $now): void
    {
        if (!$this->getStatus()->canTransitionTo(DispatchStatus::Assigned)) {
            throw new DomainException('Dispatch cannot be assigned in its current state');
        }

        $this->courier = $courier;
        $this->status = DispatchStatus::Assigned->value;
        $this->assignedAt = $now;
        $this->updatedAt = $now;
    }

    public function markInTransit(DateTimeImmutable $now): void
    {
        if (!$this->getStatus()->canTransitionTo(DispatchStatus::InTransit)) {
            throw new DomainException('Dispatch cannot move to in_transit in its current state');
        }

        $this->status = DispatchStatus::InTransit->value;
        $this->pickedUpAt = $now;
        $this->updatedAt = $now;
    }

    public function markDelivered(DateTimeImmutable $now): void
    {
        if (!$this->getStatus()->canTransitionTo(DispatchStatus::Delivered)) {
            throw new DomainException('Dispatch cannot be delivered in its current state');
        }

        $this->status = DispatchStatus::Delivered->value;
        $this->deliveredAt = $now;
        $this->updatedAt = $now;
    }
}
