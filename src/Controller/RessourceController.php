<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Folder;
use App\Entity\node\AbstractRessource;
use App\Entity\Note;
use App\Entity\Url;
use App\Entity\User;
use App\Enum\RolesEnum;
use App\Form\FileType;
use App\Form\FolderType;
use App\Form\NoteType;
use App\Form\UrlType;
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
    ) {
    }

    #[Route('/{id?}', name: 'index', defaults: ['id' => null], methods: ['GET', 'POST'])]
    public function index(Request $request, ?Folder $folder): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $folderForm = $this->createForm(FolderType::class, new Folder(), [
            'action' => $this->generateUrl('folder_create'),
            'method' => 'POST',
        ]);

        $urlForm = $this->createForm(UrlType::class, new Url(), [
            'action' => $this->generateUrl('url_create'),
            'method' => 'POST',
        ]);

        $fileForm = $this->createForm(FileType::class, new File(), [
            'action' => $this->generateUrl('file_create'),
            'method' => 'POST',
        ]);

        $noteForm = $this->createForm(NoteType::class, new Note(), [
            'action' => $this->generateUrl('note_create'),
            'method' => 'POST',
        ]);

        if ($folder && $folder->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à ce dossier.");
        }

        return $this->render('ressource/index.html.twig', [
            'folders' => $this->folderRepository->findInsideFolderByUser($currentUser, $folder),
            'ressources' => $this->ressourceRepository->findMainRessourcesForUserByFolder($currentUser, $folder),
            'folder_form' => $folderForm->createView(),
            'url_form' => $urlForm->createView(),
            'file_form' => $fileForm->createView(),
            'note_form' => $noteForm->createView(),
        ]);
    }

    #[Route('/supprimer/{id}', name: 'delete', methods: ['POST'])]
    public function deleteFolder(Request $request, AbstractRessource $ressource): Response
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
