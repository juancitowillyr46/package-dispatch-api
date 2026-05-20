<?php

declare(strict_types=1);

namespace App\Dispatch\Presentation\Request;

use Symfony\Component\Validator\Constraints as Assert;

final class AssignCourierRequest
{
    #[Assert\NotNull]
    #[Assert\Uuid]
    public string $courierId;
}
