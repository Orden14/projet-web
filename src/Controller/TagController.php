<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\User;
use App\Enum\RolesEnum;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(RolesEnum::USER->value)]
#[Route('/mes-tags', name: 'tag_')]
final class TagController extends AbstractController
{
    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag, [
            'action' => $this->generateUrl('tag_create'),
        ]);
        $form->handleRequest($request);

        return $this->render('tag/index.html.twig', [
            'tags' => $this->tagRepository->findByUser($currentUser),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/nouveau', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $currentUser */
            $currentUser = $this->getUser();
            $tag->setOwner($currentUser);

            $this->entityManager->persist($tag);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('tag_index');
    }

    #[Route('/modifier/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tag $tag): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($tag->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce tag.');
        }

        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supprimer/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Tag $tag): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($tag->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce tag.');
        }

        if ($this->isCsrfTokenValid('delete_tag_' . $tag->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($tag);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('tag_index');
    }
}
