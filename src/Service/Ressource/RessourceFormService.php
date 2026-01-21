<?php

namespace App\Service\Ressource;

use App\Entity\File;
use App\Entity\Tag;
use App\Entity\User;
use App\Interface\RessourceInterface;
use App\Util\File\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class RessourceFormService
{
    public function __construct(
        private Security $security,
        private FileManager $fileManager,
        private ParameterBagInterface $parameterBag,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function manageRessourceCreation(RessourceInterface $ressource, FormInterface $form): void
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        $ressource->setOwner($currentUser);

        $this->manageTagAssignment($ressource, $form);

        if ($ressource instanceof File) {
            $this->manageFileUpload($ressource, $form);
        }

        $this->entityManager->persist($ressource);
        $this->entityManager->flush();
    }

    private function manageTagAssignment(RessourceInterface $ressource, FormInterface $form): void
    {
        /** @var Tag[] $tags */
        $tags = $form->get('ressource')->get('tags')->getData();

        foreach ($tags as $tag) {
            $ressource->addTag($tag);
        }
    }

    private function manageFileUpload(File $file, FormInterface $form): void
    {
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $form->get('uploadFile')->getData();

        if ($uploadedFile) {
            $fileName = $this->fileManager->uploadFile(
                $uploadedFile,
                $this->parameterBag->get('uploads_directory')
            );

            $file->setFileName($fileName);
        }
    }
}
