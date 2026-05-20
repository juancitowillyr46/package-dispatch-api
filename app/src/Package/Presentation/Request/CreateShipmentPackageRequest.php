<?php

declare(strict_types=1);

namespace App\Package\Presentation\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateShipmentPackageRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public string $trackingCode;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $recipientName;

    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    public string $recipientAddress;

    #[Assert\NotNull]
    #[Assert\Positive]
    public float $weightKg;

    #[Assert\Length(max: 500)]
    public ?string $description = null;
}
