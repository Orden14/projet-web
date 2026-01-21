<?php

namespace App\Simple;

use Symfony\Component\Form\FormInterface;

final class RessourceForms
{
    private FormInterface $folderForm;
    private FormInterface $fileForm;
    private FormInterface $noteForm;
    private FormInterface $urlForm;

    public function getFolderForm(): FormInterface
    {
        return $this->folderForm;
    }

    public function setFolderForm(FormInterface $folderForm): self
    {
        $this->folderForm = $folderForm;

        return $this;
    }

    public function getFileForm(): FormInterface
    {
        return $this->fileForm;
    }

    public function setFileForm(FormInterface $fileForm): self
    {
        $this->fileForm = $fileForm;

        return $this;
    }

    public function getNoteForm(): FormInterface
    {
        return $this->noteForm;
    }

    public function setNoteForm(FormInterface $noteForm): self
    {
        $this->noteForm = $noteForm;

        return $this;
    }

    public function getUrlForm(): FormInterface
    {
        return $this->urlForm;
    }

    public function setUrlForm(FormInterface $urlForm): self
    {
        $this->urlForm = $urlForm;

        return $this;
    }
}
