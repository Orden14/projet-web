<?php

namespace App\Enum;

use App\Entity\File;
use App\Entity\Folder;
use App\Entity\Note;
use App\Entity\Url;
use App\Form\FileType;
use App\Form\FolderType;
use App\Form\NoteType;
use App\Form\UrlType;
use App\Interface\RessourceInterface;

enum RessourceTypeEnum: string
{
    case FOLDER = 'folder';
    case FILE = 'file';
    case NOTE = 'note';
    case URL = 'url';

    public function getCorrespondingRessource(): RessourceInterface
    {
        return match ($this) {
            self::FOLDER => new Folder(),
            self::FILE => new File(),
            self::URL => new Url(),
            self::NOTE => new Note(),
        };
    }

    /**
     * @return class-string<object>
     */
    public function getCorrespondingFormTypeClass(): string
    {
        return match ($this) {
            self::FOLDER => FolderType::class,
            self::FILE => FileType::class,
            self::URL => UrlType::class,
            self::NOTE => NoteType::class,
        };
    }
}
