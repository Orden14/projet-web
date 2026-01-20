<?php

namespace App\DataFixtures;

use App\Entity\Folder;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

final class FolderFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly UserRepository $userRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $tags = $this->tagRepository->findByUser($user);
            $categories = $this->categoryRepository->findByUser($user);

            $folder = (new Folder())
                ->setOwner($user)
                ->setCategory($this->faker->randomElement($categories))
                ->setTitle($this->faker->sentence(2))
                ->setDescription($this->faker->paragraph(1))
                ->addTag($this->faker->randomElement($tags))
            ;

            $manager->persist($folder);
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
            TagFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
