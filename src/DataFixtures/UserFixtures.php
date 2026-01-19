<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\RolesEnum;
use App\Service\User\UserProfileService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use FilesystemIterator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture
{
    private Generator $faker;
    private ObjectManager $manager;

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly UserProfileService $userProfileService,
        private readonly ParameterBagInterface $parameterBag,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        if ($this->kernel->getEnvironment() === 'dev') {
            $this->purgeProfilePictureDirectory();
        }

        for ($i = 0; $i < 10; $i++) {
            $this->generateCommonUser();
        }

        $manager->persist($this->generateCommonUser('user'));

        $manager->persist($this->generateAdmin());

        $manager->flush();
    }

    private function generateCommonUser(?string $presetUsername = null): User
    {
        $username = $presetUsername ?: $this->faker->firstName();

        $user = new User();

        $user->setEmail("{$username}@test.fr")
            ->setUsername($username)
            ->setPassword($this->userPasswordHasher->hashPassword($user, $username))
            ->setRole(RolesEnum::USER)
            ->setProfilePicture('preload')
        ;

        $this->manager->persist($user);

        $this->userProfileService->setDefaultProfilePicture($user);

        return $user;
    }

    private function generateAdmin(): User
    {
        $admin = new User();

        $admin
            ->setEmail('admin@test.fr')
            ->setUsername('admin')
            ->setPassword($this->userPasswordHasher->hashPassword($admin, 'admin'))
            ->setRole(RolesEnum::ADMIN)
            ->setProfilePicture('preload')
        ;

        $this->manager->persist($admin);

        $this->userProfileService->setDefaultProfilePicture($admin);

        return $admin;
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
