<?php

namespace App\Factory;

use App\Entity\CalendarEvent;
use App\Entity\User;
use App\Simple\UserOwnedEntityData;
use DateTime;

final class CalendarEventEntityFactory extends AbstractEntityFactory
{
    public function build(UserOwnedEntityData $userOwnedEntityData, DateTime $startDate, DateTime $endDate): void
    {
        $calendarEvent = new CalendarEvent();

        $calendarEvent
            ->setTitle($userOwnedEntityData->getTitle())
            ->setDescription($userOwnedEntityData->getDescription())
            ->setOwner($userOwnedEntityData->getOwner())
            ->setCategory($userOwnedEntityData->getCategory())
            ->setStartDate($startDate)
            ->setEndDate($endDate)
        ;

        $this->entity = $calendarEvent;
    }

    public function buildFreshEntity(User $owner): void
    {
        $calendarEvent = new CalendarEvent();

        $calendarEvent
            ->setOwner($owner)
            ->setStartDate(new DateTime())
            ->setEndDate(new DateTime())
        ;

        $this->entity = $calendarEvent;
    }

    /**
     * @return CalendarEvent
     */
    public function grabEntity(): object
    {
        return $this->entity ?? new CalendarEvent();
    }
}
