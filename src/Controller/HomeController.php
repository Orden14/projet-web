<?php

namespace App\Controller;

use App\Enum\RolesEnum;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(RolesEnum::USER->value)]
final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('ressource_index');
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET'])]
    public function contact(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findByUser($this->getUser());

        return $this->render('contact/contact.html.twig', [
            'contacts' => $contacts,
        ]);
    }
}
