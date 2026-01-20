<?php

namespace App\Entity\node;

use App\Entity\File;
use App\Entity\Folder;
use App\Entity\Note;
use App\Entity\Tag;
use App\Entity\Url;
use App\Interface\RessourceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'folder' => Folder::class,
    'file' => File::class,
    'note' => Note::class,
    'url' => Url::class,
])]
abstract class AbstractRessource extends AbstractUserOwnedEntity implements RessourceInterface
{
    #[ORM\ManyToOne(targetEntity: Folder::class, inversedBy: 'children')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Folder $parent = null;

    #[ORM\Column(type: 'boolean')]
    private bool $favorite = false;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'linkedRessources')]
    private Collection $tags;

    public function __construct()
    {
        parent::__construct();
        $this->tags = new ArrayCollection();
    }

    final public function getParent(): ?Folder
    {
        return $this->parent;
    }

    final public function setParent(?Folder $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    final public function getTags(): Collection
    {
        return $this->tags;
    }

    final public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addLinkedRessource($this);
        }

        return $this;
    }

    final public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeLinkedRessource($this);
        }

        return $this;
    }

    final public function isFavorite(): bool
    {
        return $this->favorite;
    }

    final public function setFavorite(bool $favorite): static
    {
        $this->favorite = $favorite;

        return $this;
    }
}
