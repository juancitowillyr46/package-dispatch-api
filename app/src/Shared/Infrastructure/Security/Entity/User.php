<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security\Entity;

use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private string $id;
    private string $email;
    /**
     * @var string[]
     */
    private array $roles = [];
    private string $password;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(string $email, array $roles = ['ROLE_USER'])
    {
        $this->id = Uuid::v7()->toRfc4122();
        $this->email = mb_strtolower($email);
        $this->roles = array_values(array_unique($roles));
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    public function setRoles(array $roles): void
    {
        $this->roles = array_values(array_unique($roles));
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function eraseCredentials(): void
    {
    }

    public function setEmail(string $email): void
    {
        $this->email = mb_strtolower($email);
        $this->updatedAt = new DateTimeImmutable();
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
