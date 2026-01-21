<?php

namespace App\Controller;

use App\Entity\Folder;
use App\Entity\node\AbstractRessource;
use App\Entity\User;
use App\Enum\RolesEnum;
use App\Factory\RessourceFormsFactory;
use App\Repository\FolderRepository;
use App\Repository\RessourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(RolesEnum::USER->value)]
#[Route('/mes-ressources', name: 'ressource_')]
final class RessourceController extends AbstractController
{
    public function __construct(
        private readonly FolderRepository $folderRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly RessourceRepository $ressourceRepository,
        private readonly RessourceFormsFactory $ressourceFormsFactory,
    ) {
    }

    #[Route('/{id?}', name: 'index', defaults: ['id' => null], methods: ['GET', 'POST'])]
    public function index(Request $request, ?Folder $folder): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $ressourceForms = $this->ressourceFormsFactory->build();

        if ($folder && $folder->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à ce dossier.");
        }

        return $this->render('ressource/index.html.twig', [
            'folders' => $this->folderRepository->findInsideFolderByUser($currentUser, $folder),
            'ressources' => $this->ressourceRepository->findMainRessourcesForUserByFolder($currentUser, $folder),
            'folder_form' => $ressourceForms->getFolderForm()->createView(),
            'file_form' => $ressourceForms->getFileForm()->createView(),
            'url_form' => $ressourceForms->getUrlForm()->createView(),
            'note_form' => $ressourceForms->getNoteForm()->createView(),
        ]);
    }

    #[Route('/supprimer/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, AbstractRessource $ressource): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($ressource->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à ce dossier.");
        }

        $parentFolder = $ressource->getParent();

        if ($this->isCsrfTokenValid('delete' . $ressource->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($ressource);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('ressource_index', ['id' => $parentFolder?->getId()]);
    }
}
