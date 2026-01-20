<?php

namespace App\Entity;

use App\Entity\node\AbstractUserOwnedEntity;
use App\Repository\CalendarEventRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarEventRepository::class)]
class CalendarEvent extends AbstractUserOwnedEntity
{
    #[ORM\Column]
    private DateTime $startDate;

    #[ORM\Column]
    private DateTime $endDate;

    final public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    final public function setStartDate(DateTime $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    final public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    final public function setEndDate(DateTime $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }
}
