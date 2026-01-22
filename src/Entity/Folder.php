<?php

namespace App\Entity;

use App\Entity\node\AbstractRessource;
use App\Repository\FolderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FolderRepository::class)]
class Folder extends AbstractRessource
{
    /**
     * @var Collection<int, AbstractRessource>
     */
    #[ORM\OneToMany(targetEntity: AbstractRessource::class, mappedBy: 'parent', cascade: ['persist', 'remove'])]
    private Collection $children;

    public function __construct()
    {
        parent::__construct();
        $this->children = new ArrayCollection();
    }

    /**
     * @return Collection<int, AbstractRessource>
     */
    final public function getChildren(): Collection
    {
        return $this->children;
    }
}
