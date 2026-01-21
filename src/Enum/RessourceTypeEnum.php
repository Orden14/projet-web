<?php

namespace App\Enum;

enum RessourceTypeEnum: string
{
    case FOLDER = 'folder';
    case FILE = 'file';
    case NOTE = 'note';
    case URL = 'url';
}
