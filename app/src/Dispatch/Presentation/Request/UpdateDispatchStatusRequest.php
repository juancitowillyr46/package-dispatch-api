<?php

declare(strict_types=1);

namespace App\Dispatch\Presentation\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateDispatchStatusRequest
{
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['in_transit', 'delivered'])]
    public string $status;
}
