<?php

namespace App\DataFixtures;

use App\DataFixtures\util\UserOwnedEntityDataGenerator;
use App\Entity\Contact;
use App\Entity\User;
use App\Factory\ContactFactory;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

final class ContactFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct(
        private readonly ContactFactory $contactFactory,
        private readonly UserRepository $userRepository,
        private readonly UserOwnedEntityDataGenerator $userOwnedEntityDataGenerator,
    ) {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) {
                $manager->persist($this->generateContactForUser($user));
            }
        }

        $manager->flush();
    }

    private function generateContactForUser(User $user): Contact
    {
        $userOwnedEntityData = $this->userOwnedEntityDataGenerator->generateRandomData($user);

        $this->contactFactory->build(
            $userOwnedEntityData,
            $this->faker->name(),
            $this->faker->phoneNumber(),
            $this->faker->email()
        );

        return $this->contactFactory->grabEntity();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
