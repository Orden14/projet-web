<?php

namespace App\DataFixtures;

use App\Entity\Url;
use App\Repository\CategoryRepository;
use App\Repository\FolderRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

final class UrlFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly UserRepository $userRepository,
        private readonly FolderRepository $folderRepository,
        private readonly CategoryRepository $categoryRepository,
    ) {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $folders = $this->folderRepository->findByUser($user);
            $tags = $this->tagRepository->findByUser($user);
            $categories = $this->categoryRepository->findByUser($user);

            for ($i = 0; $i < 8; $i++) {
                $rand = $this->faker->numberBetween(0, 1);
                $file = (new Url())
                    ->setOwner($user)
                    ->setParent($rand === 1 ? $this->faker->randomElement($folders) : null)
                    ->setCategory($this->faker->randomElement($categories))
                    ->setTitle($this->faker->sentence(2))
                    ->setDescription($this->faker->paragraph(1))
                    ->addTag($this->faker->randomElement($tags))
                    ->setUrl($this->faker->url())
                ;

                $manager->persist($file);
            }

            $manager->flush();
        }
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            FolderFixtures::class,
            TagFixtures::class,
            CategoryFixtures::class,
        ];
    }
}
