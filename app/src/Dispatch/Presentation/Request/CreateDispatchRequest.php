<?php

declare(strict_types=1);

namespace App\Dispatch\Presentation\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateDispatchRequest
{
    #[Assert\NotNull]
    #[Assert\Uuid]
    public string $packageId;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public string $referenceNumber;

    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    public string $originAddress;

    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    public string $destinationAddress;

    #[Assert\Length(max: 500)]
    public ?string $notes = null;
}
