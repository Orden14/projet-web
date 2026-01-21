<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\RolesEnum;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(RolesEnum::USER->value)]
#[Route('/mes-contacts', name: 'contact_')]
final class ContactController extends AbstractController
{
    public function __construct(
        private readonly ContactRepository $contactRepository,
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
}
