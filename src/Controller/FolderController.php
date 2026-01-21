<?php

namespace App\Controller;

use App\Entity\Folder;
use App\Entity\User;
use App\Form\FolderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mes-ressources', name: 'folder_')]
final class FolderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/nouveau/dossier', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $folder = new Folder();
        $form = $this->createForm(FolderType::class, $folder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $folder->setOwner($currentUser);

            $this->entityManager->persist($folder);
            $this->entityManager->flush();

        }

        return $this->redirectToRoute('ressource_index', ['id' => $folder->getParent()?->getId()]);
    }
}
