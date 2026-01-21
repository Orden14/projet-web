<?php

namespace App\Entity\node;

use App\Entity\Category;
use App\Entity\User;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class AbstractUserOwnedEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $creationDate;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updateDate = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    public function __construct()
    {
        $this->creationDate = new DateTime();
    }

    final public function getId(): ?int
    {
        return $this->id;
    }

    final public function getOwner(): User
    {
        return $this->owner;
    }

    final public function setOwner(User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    final public function getTitle(): string
    {
        return $this->title;
    }

    final public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    final public function getDescription(): ?string
    {
        return $this->description;
    }

    final public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    final public function getCreationDate(): ?DateTimeInterface
    {
        return $this->creationDate;
    }

    final public function setCreationDate(DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    final public function getUpdateDate(): ?DateTimeInterface
    {
        return $this->updateDate;
    }

    final public function setUpdateDate(DateTimeInterface $updateDate): static
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    final public function getCategory(): Category
    {
        return $this->category;
    }

    final public function setCategory(Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
