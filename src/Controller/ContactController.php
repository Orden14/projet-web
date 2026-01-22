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
    public function index(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact, [
            'action' => $this->generateUrl('contact_create'),
        ]);
        $form->handleRequest($request);

        return $this->render('contact/index.html.twig', [
            'contacts' => $this->contactRepository->findByUser($currentUser),
            'form' => $form->createView(),
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

    #[Route('/modifier/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact): Response
    {
        $currentUser = $this->getUser();
        if ($contact->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce contact.');
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('contact_index');
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supprimer/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact): Response
    {
        $currentUser = $this->getUser();
        if ($contact->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce contact.');
        }

        if ($this->isCsrfTokenValid('delete' . $contact->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($contact);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('contact_index');
    }

    #[Route('/{id}', name: 'detail', methods: ['GET'])]
    public function detail(Contact $contact): Response
    {
        $currentUser = $this->getUser();
        if ($contact->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à ce contact.');
        }

        return $this->render('contact/detail.html.twig', [
            'contact' => $contact,
        ]);
    }
}
