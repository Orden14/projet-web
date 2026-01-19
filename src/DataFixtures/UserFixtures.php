<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\RolesEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $manager->persist($this->generateCommonUser());
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
        ;

        return $user;
    }

    private function generateAdmin(): User
    {
        $admin = new User();

        return $admin
            ->setEmail('admin@test.fr')
            ->setUsername('admin')
            ->setPassword($this->userPasswordHasher->hashPassword($admin, 'admin'))
            ->setRole(RolesEnum::ADMIN)
        ;
    }
}
