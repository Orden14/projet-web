<?php

namespace App\Util\Ressource;

use App\Entity\Folder;

final readonly class RessourceBreadcrumbUtil
{
    /**
     * @return Folder[]
     */
    public static function generateBreadcrumb(?Folder $folder): array
    {
        $breadcrumb = [];
        while ($folder) {
            $breadcrumb[] = $folder;
            $folder = $folder->getParent();
        }

        return array_reverse($breadcrumb);
    }
}
