<?php

declare(strict_types=1);

namespace App\Package\Domain\Entity;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final class ShipmentPackage
{
    private string $id;
    private string $trackingCode;
    private string $recipientName;
    private string $recipientAddress;
    private float $weightKg;
    private ?string $description;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $trackingCode,
        string $recipientName,
        string $recipientAddress,
        float $weightKg,
        ?string $description = null,
    ) {
        $this->id = Uuid::v7()->toRfc4122();
        $this->trackingCode = $trackingCode;
        $this->recipientName = $recipientName;
        $this->recipientAddress = $recipientAddress;
        $this->weightKg = $weightKg;
        $this->description = $description;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTrackingCode(): string
    {
        return $this->trackingCode;
    }

    public function getRecipientName(): string
    {
        return $this->recipientName;
    }

    public function getRecipientAddress(): string
    {
        return $this->recipientAddress;
    }

    public function getWeightKg(): float
    {
        return $this->weightKg;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
