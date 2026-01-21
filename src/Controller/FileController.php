<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\User;
use App\Form\FileType;
use App\Util\File\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mes-ressources', name: 'file_', methods: ['GET', 'POST'])]
final class FileController extends AbstractController
{
    public function __construct(
        private readonly FileManager $fileManager,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/nouveau/fichier', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $file = new File();
        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file->setOwner($currentUser);

            $uploadedFIle = $form->get('uploadFile')->getData();
            $file->setFileName($this->fileManager->uploadFile($uploadedFIle, $this->getParameter('uploads_directory')));

            $this->entityManager->persist($file);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('ressource_index', ['id' => $file->getParent()?->getId()]);
    }
}
