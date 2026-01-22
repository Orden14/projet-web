<?php

namespace App\EventSubscriber;

use App\Entity\CalendarEvent;
use App\Entity\User;
use App\Repository\CalendarEventRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class CalendarSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private UrlGeneratorInterface $router,
        private CalendarEventRepository $calendarEventRepository,
    ) {
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(\CalendarBundle\Event\CalendarEvent $calendar): void
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        $start = $calendar->getStart();
        $end = $calendar->getEnd();

        /** @var CalendarEvent[] $calendarEvents */
        $calendarEvents = $this->calendarEventRepository->findByPeriodForUser($currentUser, $start, $end);

        foreach ($calendarEvents as $calendarEvent) {
            $event = new Event(
                $calendarEvent->getTitle(),
                $calendarEvent->getStartDate(),
                $calendarEvent->getEndDate(),
            );

            $event->setOptions([
                'backgroundColor' => $calendarEvent->getColor(),
                'borderColor' => $calendarEvent->getColor(),
            ]);

            $event->addOption(
                'url',
                $this->router->generate('calendar_event_show', [
                    'id' => $calendarEvent->getId(),
                ])
            );

            $event->addOption('eventId', $calendarEvent->getId());

            $calendar->addEvent($event);
        }
    }
}
