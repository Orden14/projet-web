<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Folder;
use App\Entity\Note;
use App\Entity\Url;
use App\Entity\User;
use App\Enum\RessourceTypeEnum;
use App\Enum\RolesEnum;
use App\Factory\RessourceFormFactory;
use App\Repository\CategoryRepository;
use App\Repository\FolderRepository;
use App\Repository\RessourceRepository;
use App\Service\Ressource\RessourceFormService;
use App\Util\File\FileManager;
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
        private readonly FileManager $fileManager,
        private readonly FolderRepository $folderRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly RessourceRepository $ressourceRepository,
        private readonly RessourceFormService $ressourceFormService,
        private readonly RessourceFormFactory $ressourceFormsFactory,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    #[Route('/{id?}', name: 'index', defaults: ['id' => null], methods: ['GET', 'POST'])]
    public function index(?Folder $folder): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($folder && $folder->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à ce dossier.");
        }

        $breadcrumb = [];
        $cursor = $folder;
        while ($cursor) {
            $breadcrumb[] = $cursor;
            $cursor = $cursor->getParent();
        }
        $breadcrumb = array_reverse($breadcrumb);

        return $this->render('ressource/index.html.twig', [
            'current_folder' => $folder,
            'folders' => $this->folderRepository->findInsideFolderByUser($currentUser, $folder),
            'ressources' => $this->ressourceRepository->findMainRessourcesForUserByFolder($currentUser, $folder),
            'categories' => $this->categoryRepository->findByUser($currentUser),
            'folder_form' => $this->ressourceFormsFactory->build(new Folder())->createView(),
            'file_form' => $this->ressourceFormsFactory->build(new File())->createView(),
            'url_form' => $this->ressourceFormsFactory->build(new Url())->createView(),
            'note_form' => $this->ressourceFormsFactory->build(new Note())->createView(),
            'breadcrumb' => $breadcrumb,
        ]);
    }

    #[Route('/nouveau/{type}', name: 'create', methods: ['POST'])]
    public function create(Request $request, string $type): Response
    {
        $typeEnum = RessourceTypeEnum::from($type);

        $ressource = $typeEnum->getCorrespondingRessource();

        $form = $this->ressourceFormsFactory->build($ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ressourceFormService->manageRessourceCreation($ressource, $form);
        }

        return $this->redirectToRoute('ressource_index', ['id' => $ressource->getParent()?->getId()]);
    }

    #[Route('/modifier/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $ressource = $this->ressourceRepository->find($id);

        $form = $this->ressourceFormsFactory->build($ressource, true);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ressourceFormService->manageRessourceEdition($ressource, $form);

            return $this->redirectToRoute('ressource_index', ['id' => $ressource->getParent()?->getId()]);
        }

        return $this->render('ressource/edit.html.twig', [
            'ressource' => $ressource,
            'form' => $form->createView(),
            'form_template_path' => $this->ressourceFormService->getFormTemplatePath($ressource->getType()),
        ]);
    }

    #[Route('/detail/{id}', name: 'detail', methods: ['GET'])]
    public function detail(int $id): Response
    {
        $ressource = $this->ressourceRepository->find($id);

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($ressource->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à cette ressource.");
        }

        return $this->render('ressource/detail.html.twig', [
            'ressource' => $ressource,
        ]);
    }

    #[Route('/toggle-favorite/{id}', name: 'toggle_favorite', methods: ['POST'])]
    public function toggleFavorite(Request $request, int $id): Response
    {
        $ressource = $this->ressourceRepository->find($id);

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($ressource->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à cette ressource.");
        }

        if ($this->isCsrfTokenValid('favorite' . $ressource->getId(), $request->request->get('_token'))) {
            $ressource->setFavorite(!$ressource->isFavorite());
            $this->entityManager->flush();
        }

        return $this->json(['favorite' => $ressource->isFavorite()]);
    }

    #[Route('/supprimer/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, int $id): Response
    {
        $ressource = $this->ressourceRepository->find($id);

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($ressource->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException("Vous n'avez pas accès à ce dossier.");
        }

        $parentFolder = $ressource->getParent();

        if ($this->isCsrfTokenValid('delete' . $ressource->getId(), $request->request->get('_token'))) {
            if ($ressource instanceof File) {
                $this->fileManager->removeFile($ressource->getFileName(), $this->getParameter('uploads_directory'));
            }

            $this->entityManager->remove($ressource);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('ressource_index', ['id' => $parentFolder?->getId()]);
    }
}
