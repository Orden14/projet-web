<?php

namespace App\Controller;

use App\Entity\CalendarEvent;
use App\Entity\User;
use App\Enum\RolesEnum;
use App\Factory\CalendarEventEntityFactory;
use App\Form\CalendarEventType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(RolesEnum::USER->value)]
#[Route('/calendrier', name: 'calendar_')]
final class CalendarController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CalendarEventEntityFactory $calendarEventEntityFactory,
    ) {
    }

    #[Route('/', name: 'index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $this->calendarEventEntityFactory->buildFreshEntity($currentUser);
        $calendarEvent = $this->calendarEventEntityFactory->grabEntity();

        $form = $this->createForm(CalendarEventType::class, $calendarEvent, [
            'action' => $this->generateUrl('calendar_event_new'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        return $this->render('calendar/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/evenement/{id}', name: 'event_show', methods: ['GET'])]
    public function showEvent(CalendarEvent $calendarEvent): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($calendarEvent->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException("Vous n'avez pas la permission de voir cet évènement.");
        }

        return $this->render('calendar/show.html.twig', [
            'event' => $calendarEvent,
        ]);
    }

    #[Route('/nouvel-evenement', name: 'event_new', methods: ['POST'])]
    public function createEvent(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $this->calendarEventEntityFactory->buildFreshEntity($currentUser);
        $calendarEvent = $this->calendarEventEntityFactory->grabEntity();

        $form = $this->createForm(CalendarEventType::class, $calendarEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($calendarEvent);
            $this->entityManager->flush();

        }

        return $this->redirectToRoute('calendar_index');
    }

    #[Route('/modifier-evenement/{id}', name: 'event_edit', methods: ['GET', 'POST'])]
    public function editEvent(Request $request, CalendarEvent $calendarEvent): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($calendarEvent->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException("Vous n'avez pas la permission de modifier cet évènement.");
        }

        $form = $this->createForm(CalendarEventType::class, $calendarEvent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $calendarEvent->setUpdateDate(new DateTime());
            $this->entityManager->flush();

            return $this->redirectToRoute('calendar_event_show', ['id' => $calendarEvent->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $calendarEvent,
        ]);
    }

    #[Route('/supprimer-evenement/{id}', name: 'event_delete', methods: ['POST'])]
    public function deleteEvent(Request $request, CalendarEvent $calendarEvent): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($calendarEvent->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException("Vous n'avez pas la permission de supprimer cet évènement.");
        }

        if ($this->isCsrfTokenValid('delete' . $calendarEvent->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($calendarEvent);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('calendar_index', [], Response::HTTP_SEE_OTHER);
    }
}
