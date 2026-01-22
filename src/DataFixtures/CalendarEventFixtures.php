<?php

namespace App\DataFixtures;

use App\DataFixtures\util\UserOwnedEntityDataGenerator;
use App\Entity\CalendarEvent;
use App\Entity\User;
use App\Factory\CalendarEventEntityFactory;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Random\RandomException;

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

    private const COLORS = [
        '#32a89e',
        '#9ff246',
        '#53f5bf',
        '#f5b253',
    ];

    /**
     * @throws RandomException
     */
    public function load(ObjectManager $manager): void
    {
        /** @var User $admin */
        $admin = $this->userRepository->findOneBy(['username' => 'admin']);
        /** @var User $testUser */
        $testUser = $this->userRepository->findOneBy(['username' => 'user']);

        $mondayLastWeek = new DateTime('monday last week');

        foreach ([$admin, $testUser] as $user) {
            $date = (clone $mondayLastWeek)->modify('+ 1 day')->setTime(random_int(9, 21), random_int(0, 59));
            for ($i = 0; $i < 20; $i++) {
                $date->modify('+ 1 day')->setTime(random_int(9, 21), random_int(0, 59));
                $manager->persist($this->createCalendarEventForUser($user, $date));
            }
        }

        $manager->flush();
    }

    private function createCalendarEventForUser(User $user, DateTime $startDate): CalendarEvent
    {
        $userOwnedEntityData = $this->userOwnedEntityDataGenerator->generateRandomData($user, true);

        $this->calendarEventFactory->build($userOwnedEntityData, clone $startDate, (clone $startDate)->modify('+ 2 hours'));

        return $this->calendarEventFactory->grabEntity()->setColor($this->faker->randomElement(self::COLORS));
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
