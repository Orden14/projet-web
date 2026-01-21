<?php

namespace App\DataFixtures;

use App\DataFixtures\util\UserOwnedEntityDataGenerator;
use App\Entity\CalendarEvent;
use App\Entity\User;
use App\Factory\CalendarEventEntityFactory;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

final class CalendarEventFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CalendarEventEntityFactory $calendarEventFactory,
        private readonly UserOwnedEntityDataGenerator $userOwnedEntityDataGenerator,
    ) {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            for ($i = 0; $i < 6; $i++) {
                $manager->persist($this->createCalendarEventForUser($user));
            }
        }

        $manager->flush();
    }

    private function createCalendarEventForUser(User $user): CalendarEvent
    {
        $userOwnedEntityData = $this->userOwnedEntityDataGenerator->generateRandomData($user);
        $randomStartDate = $this->faker->dateTimeBetween('-1 months', '+1 months');
        $randomEndDate = $this->faker->dateTimeBetween($randomStartDate, '+1 months');

        $this->calendarEventFactory->build($userOwnedEntityData, $randomStartDate, $randomEndDate);

        return $this->calendarEventFactory->grabEntity();
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
