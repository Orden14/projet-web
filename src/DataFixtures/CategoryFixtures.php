<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    private const CATEGORIES = ['Personnel', 'Travail', 'Loisirs'];

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CategoryFactory $categoryFactory,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            foreach (self::CATEGORIES as $category) {
                $this->categoryFactory->build($category, $user);
                $manager->persist($this->categoryFactory->grabEntity());
            }
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
