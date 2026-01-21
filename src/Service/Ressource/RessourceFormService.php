<?php

namespace App\Service\Ressource;

use App\Entity\File;
use App\Entity\Tag;
use App\Entity\User;
use App\Enum\RessourceTypeEnum;
use App\Interface\RessourceInterface;
use App\Util\File\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
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

    public function manageRessourceEdition(RessourceInterface $ressource, FormInterface $form): void
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        if ($ressource->getOwner() !== $currentUser) {
            throw new LogicException("Vous n'avez pas la permission d'éditer cette ressource.");
        }

        $this->manageTagAssignment($ressource, $form);

        if ($ressource instanceof File) {
            $this->manageFileUpload($ressource, $form);
        }

        $this->entityManager->flush();
    }

    public function getFormTemplatePath(RessourceTypeEnum $ressourceType): string
    {
        return match ($ressourceType) {
            RessourceTypeEnum::FOLDER => 'ressource/form/_folder_form.html.twig',
            RessourceTypeEnum::FILE => 'ressource/form/_file_form.html.twig',
            RessourceTypeEnum::URL => 'ressource/form/_url_form.html.twig',
            RessourceTypeEnum::NOTE => 'ressource/form/_note_form.html.twig',
        };
    }

    private function manageTagAssignment(RessourceInterface $ressource, FormInterface $form): void
    {
        /** @var Tag[] $selectedTags */
        $selectedTags = $form->get('ressource')->get('tags')->getData();

        foreach ($selectedTags as $selectedTag) {
            $ressource->addTag($selectedTag);
        }

        foreach ($ressource->getTags() as $tag) {
            if (!in_array($tag, $selectedTags, true)) {
                $ressource->removeTag($tag);
            }
        }
    }

    private function manageFileUpload(File $file, FormInterface $form): void
    {
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $form->get('uploadFile')->getData();

        if ($uploadedFile) {
            $this->fileManager->removeFile(
                $file->getFileName(),
                $this->parameterBag->get('uploads_directory')
            );

            $fileName = $this->fileManager->uploadFile(
                $uploadedFile,
                $this->parameterBag->get('uploads_directory')
            );

            $file->setFileName($fileName);
        }
    }
}
