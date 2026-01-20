<?php

namespace App\Entity;

use App\Entity\node\AbstractRessource;
use App\Repository\NoteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note extends AbstractRessource
{
    #[ORM\Column(type: Types::TEXT)]
    private string $content;

    final public function getContent(): string
    {
        return $this->content;
    }

    final public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
