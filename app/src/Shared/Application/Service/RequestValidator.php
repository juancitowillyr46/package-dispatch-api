<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Domain\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestValidator
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function validate(object $request): void
    {
        $violations = $this->validator->validate($request);

        if (0 === count($violations)) {
            return;
        }

        $errors = [];
        foreach ($violations as $violation) {
            $path = $violation->getPropertyPath() ?: 'request';
            $errors[$path][] = (string) $violation->getMessage();
        }

        throw new ValidationException($errors);
    }
}
