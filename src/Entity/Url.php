<?php

namespace App\Entity;

use App\Entity\node\AbstractRessource;
use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrlRepository::class)]
class Url extends AbstractRessource
{
    #[ORM\Column(length: 255)]
    private string $url;

    final public function getUrl(): string
    {
        return $this->url;
    }

    final public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
