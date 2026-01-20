<?php

namespace App\Entity;

use App\Entity\node\AbstractRessource;
use App\Interface\RessourceInterface;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 255)]
    private string $color;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    /**
     * @var Collection<int, RessourceInterface>
     */
    #[ORM\ManyToMany(targetEntity: AbstractRessource::class, inversedBy: 'tags')]
    #[ORM\JoinTable(name: 'ressource_tag')]
    private Collection $linkedRessources;

    public function __construct()
    {
        $this->linkedRessources = new ArrayCollection();
    }

    final public function getId(): ?int
    {
        return $this->id;
    }

    final public function getTitle(): string
    {
        return $this->title;
    }

    final public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    final public function getColor(): string
    {
        return $this->color;
    }

    final public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    final public function getOwner(): User
    {
        return $this->owner;
    }

    final public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, RessourceInterface>
     */
    final public function getLinkedRessources(): Collection
    {
        return $this->linkedRessources;
    }

    final public function addLinkedRessource(RessourceInterface $linkedRessource): self
    {
        if (!$this->linkedRessources->contains($linkedRessource)) {
            $this->linkedRessources->add($linkedRessource);
        }

        return $this;
    }

    final public function removeLinkedRessource(RessourceInterface $linkedRessource): self
    {
        $this->linkedRessources->removeElement($linkedRessource);

        return $this;
    }
}
