<?php

namespace App\DataFixtures\util;

use App\Util\File\FileManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\KernelInterface;

final readonly class FileMockUploader
{
    public function __construct(
        private KernelInterface $kernel,
        private FileManager $fileManager,
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function mockFileUpload(): string
    {
        $faker = Factory::create();

        if ($this->kernel->getEnvironment() !== 'dev') {
            return 'emptyFileForTest.pdf';
        }

        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'ressources' . DIRECTORY_SEPARATOR . 'dummy.pdf';
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . basename($filePath);
        copy($filePath, $tempPath);

        return $this->fileManager->uploadFile(
            new File($tempPath, true),
            $this->parameterBag->get('uploads_directory')
        );
    }
}
