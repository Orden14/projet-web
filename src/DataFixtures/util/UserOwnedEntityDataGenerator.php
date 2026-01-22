<?php

namespace App\DataFixtures\util;

use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Simple\UserOwnedEntityData;
use Faker\Factory;

final class UserOwnedEntityDataGenerator
{
    /** @var Category[] */
    private array $categories = [];

    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    public function generateRandomData(User $user, bool $calendarEvent = false): UserOwnedEntityData
    {
        $faker = Factory::create();

        if (empty($this->categories) || reset($this->categories)->getOwner() !== $user) {
            $this->categories = $this->categoryRepository->findByUser($user);
        }

        return (new UserOwnedEntityData())
            ->setOwner($user)
            ->setTitle($calendarEvent ? $faker->word() : $faker->jobTitle)
            ->setDescription($faker->realText())
            ->setCategory($faker->randomElement($this->categories))
        ;
    }
}
