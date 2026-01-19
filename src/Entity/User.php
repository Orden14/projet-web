<?php

namespace App\Entity;

use App\Enum\RolesEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $username = null;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    final public function getId(): ?int
    {
        return $this->id;
    }

    final public function getEmail(): ?string
    {
        return $this->email;
    }

    final public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    final public function getUsername(): ?string
    {
        return $this->username;
    }

    final public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    final public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     *
     * @deprecated use getRole() instead
     */
    final public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     *
     * @deprecated use setRole() instead
     */
    final public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    final public function getRole(): RolesEnum
    {
        return RolesEnum::from($this->roles[0]);
    }

    final public function setRole(RolesEnum $role): self
    {
        $this->roles = [$role->value];

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    final public function getPassword(): ?string
    {
        return $this->password;
    }

    final public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    final public function eraseCredentials(): void
    {
        // Unimplemented
    }
}
