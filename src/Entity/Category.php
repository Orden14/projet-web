<?php

namespace App\Entity;

use App\Entity\node\AbstractRessource;
use App\Interface\RessourceInterface;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    /**
     * @var Collection<int, RessourceInterface>
     */
    #[ORM\ManyToMany(targetEntity: AbstractRessource::class, inversedBy: 'categories')]
    #[ORM\JoinTable(name: 'ressource_category')]
    private Collection $linkedRessources;

    public function __construct()
    {
        $this->linkedRessources = new ArrayCollection();
    }

    final public function getId(): ?int
    {
        return $this->id;
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public function setName(string $name): self
    {
        $this->name = $name;

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
