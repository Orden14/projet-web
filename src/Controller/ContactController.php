<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Enum\RolesEnum;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(RolesEnum::USER->value)]
#[Route('/mes-contacts', name: 'contact_')]
final class ContactController extends AbstractController
{
    public function __construct(
        private readonly ContactRepository $contactRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        return $this->render('contact/contact.html.twig', [
            'contacts' => $this->contactRepository->findByUser($currentUser),
        ]);
    }

    #[Route('/nouveau', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $currentUser */
            $currentUser = $this->getUser();

            $contact->setOwner($currentUser);

            $this->entityManager->persist($contact);
            $this->entityManager->flush();

        }

        return $this->redirectToRoute('contact_index');
    }

    public function edit(Request $request, Contact $contact): Response
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('contact_index');
        }

        return $this->render('contact/edit_contact.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }
}
