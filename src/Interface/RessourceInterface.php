<?php

namespace App\Interface;

use App\Entity\Category;
use App\Entity\Folder;
use App\Entity\Tag;
use App\Entity\User;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;

interface RessourceInterface
{
    public function getOwner(): User;

    public function setOwner(User $owner): static;

    public function getTitle(): string;

    public function setTitle(string $title): static;

    public function getDescription(): string;

    public function setDescription(string $description): static;

    public function getCreationDate(): ?DateTimeInterface;

    public function setCreationDate(DateTimeInterface $creationDate): static;

    public function getUpdateDate(): DateTimeInterface;

    public function setUpdateDate(DateTimeInterface $updateDate): static;

    public function getCategory(): Category;

    public function setCategory(Category $category): static;

    public function getParent(): ?Folder;

    public function setParent(?Folder $parent): static;

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection;

    public function addTag(Tag $tag): static;

    public function removeTag(Tag $tag): static;

    public function isFavorite(): bool;

    public function setFavorite(bool $favorite): static;
}
