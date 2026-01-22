<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Enum\RolesEnum;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(RolesEnum::USER->value)]
#[Route('/mes-categories', name: 'category_')]
final class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('category_create'),
        ]);
        $form->handleRequest($request);

        return $this->render('category/index.html.twig', [
            'categories' => $this->categoryRepository->findByUser($currentUser),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/nouvelle', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $currentUser */
            $currentUser = $this->getUser();
            $category->setOwner($currentUser);

            $this->entityManager->persist($category);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('category_index');
    }

    #[Route('/modifier/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($category->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette categorie.');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supprimer/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Category $category): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($category->getOwner() !== $currentUser) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette categorie.');
        }

        if ($this->isCsrfTokenValid('delete_category_' . $category->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('category_index');
    }
}
