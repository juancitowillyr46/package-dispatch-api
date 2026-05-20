<?php

declare(strict_types=1);

namespace App\Courier\Presentation\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateCourierRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $fullName;

    #[Assert\Length(max: 50)]
    public ?string $phone = null;

    public bool $active = true;
}
