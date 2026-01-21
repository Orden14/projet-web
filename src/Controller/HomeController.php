<?php

namespace App\Controller;

use App\Enum\RolesEnum;
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
        return $this->render('ressources/index.html.twig');
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET'])]
    public function contact(): Response
    {
        return $this->render('contact/contact.html.twig');
    }
}
