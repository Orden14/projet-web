<?php

namespace App\Entity;

use App\Entity\node\AbstractUserOwnedEntity;
use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact extends AbstractUserOwnedEntity
{
    #[ORM\Column(length: 255)]
    private string $contactName;

    #[ORM\Column(length: 255)]
    private string $contactNumber;

    #[ORM\Column(length: 255)]
    private string $contactEmail;

    final public function getContactName(): string
    {
        return $this->contactName;
    }

    final public function setContactName(string $contactName): self
    {
        $this->contactName = $contactName;

        return $this;
    }

    final public function getContactNumber(): string
    {
        return $this->contactNumber;
    }

    final public function setContactNumber(string $contactNumber): self
    {
        $this->contactNumber = $contactNumber;

        return $this;
    }

    final public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    final public function setContactEmail(string $contactEmail): self
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }
}
