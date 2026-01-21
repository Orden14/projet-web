<?php

namespace App\Factory;

use App\Entity\File;
use App\Entity\Folder;
use App\Entity\Note;
use App\Entity\Url;
use App\Form\FileType;
use App\Form\FolderType;
use App\Form\NoteType;
use App\Form\UrlType;
use App\Simple\RessourceForms;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class RessourceFormsFactory
{
    public function __construct(
        private RouterInterface $router,
        private FormFactoryInterface $formFactory,
    ) {
    }

    public function build(): RessourceForms
    {
        $folderForm = $this->formFactory->create(FolderType::class, new Folder(), [
            'action' => $this->router->generate('folder_create'),
            'method' => 'POST',
        ]);

        $fileForm = $this->formFactory->create(FileType::class, new File(), [
            'action' => $this->router->generate('file_create'),
            'method' => 'POST',
        ]);

        $urlForm = $this->formFactory->create(UrlType::class, new Url(), [
            'action' => $this->router->generate('url_create'),
            'method' => 'POST',
        ]);

        $noteForm = $this->formFactory->create(NoteType::class, new Note(), [
            'action' => $this->router->generate('note_create'),
            'method' => 'POST',
        ]);

        return (new RessourceForms())
            ->setFolderForm($folderForm)
            ->setFileForm($fileForm)
            ->setUrlForm($urlForm)
            ->setNoteForm($noteForm)
        ;
    }
}
