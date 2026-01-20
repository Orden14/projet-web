<?php

namespace App\Entity;

use App\Entity\node\AbstractRessource;
use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File extends AbstractRessource
{
    #[ORM\Column(length: 255)]
    private string $fileName;

    final public function getFileName(): string
    {
        return $this->fileName;
    }

    final public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }
}
