<?php

namespace App\DataFixtures;

use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use FilesystemIterator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class UserFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly UserFactory $userFactory,
        private readonly ParameterBagInterface $parameterBag,
    ) {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        if ($this->kernel->getEnvironment() === 'dev') {
            $this->purgeProfilePictureDirectory();
        }

        $this->generateAdmin();
        $this->generateCommonUser('user');

        for ($i = 0; $i < 10; $i++) {
            $this->generateCommonUser();
        }
    }

    private function generateCommonUser(?string $presetUsername = null): void
    {
        $username = $presetUsername ?: $this->faker->firstName();

        $this->userFactory->build(
            "{$username}@test.fr",
            $username,
            $username,
        );
    }

    private function generateAdmin(): void
    {
        $this->userFactory->build(
            'admin@test.fr',
            'admin',
            'admin',
            true,
        );
    }

    private function purgeProfilePictureDirectory(): void
    {
        $files = new FilesystemIterator($this->parameterBag->get('profile_picture_directory'));

        foreach ($files as $file) {
            if ($file->isFile() && $file->getFilename() !== '.gitignore') {
                unlink($file->getPathname());
            }
        }
    }
}
