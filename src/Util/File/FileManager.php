<?php

namespace App\Util\File;

use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

final readonly class FileManager
{
    /**
     * @throws FileException
     */
    public function uploadFile(File $file, string $directory): string
    {
        $extension = $file->guessExtension();

        if ($extension === null && method_exists($file, 'getClientOriginalExtension')) {
            $extension = $file->getClientOriginalExtension() ?: 'bin';
        }

        $fileName = uniqid('file', true) . '.' . $extension;

        try {
            $file->move(
                $directory,
                $fileName
            );
        } catch (FileException $e) {
            throw new FileException($e->getMessage(), $e->getCode());
        }

        return $fileName;
    }

    public function removeFile(string $fileName, string $directory): void
    {
        try {
            unlink($directory . DIRECTORY_SEPARATOR . $fileName);
        } catch (Exception) {
            // Do nothing, file does not exist.
        }
    }
}
