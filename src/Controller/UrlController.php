<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Url;
use App\Entity\User;
use App\Form\UrlType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mes-ressources', name: 'url_')]
final class UrlController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/nouveau/url', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $url = new Url();
        $form = $this->createForm(UrlType::class, $url);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $url->setOwner($currentUser);

            /** @var Tag[] $tags */
            $tags = $form->get('ressource')->get('tags')->getData();

            foreach ($tags as $tag) {
                $url->addTag($tag);
            }

            $this->entityManager->persist($url);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('ressource_index', ['id' => $url->getParent()?->getId()]);
    }
}
