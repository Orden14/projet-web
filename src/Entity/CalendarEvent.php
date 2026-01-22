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

    #[ORM\Column(length: 255)]
    private string $color;

    public function __construct()
    {
        parent::__construct();
        $this->color = '#32a89e';
    }

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

    final public function getColor(): string
    {
        return $this->color;
    }

    final public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }
}
