<?php

namespace App\DataFixtures;

use App\Factory\TagFactory;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class TagFixtures extends Fixture implements DependentFixtureInterface
{
    private const TAGS = [
        'Bleu' => '#a5d2ff',
        'Rouge' => '#ff4e5b',
    ];

    public function __construct(
        private readonly TagFactory $tagFactory,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            foreach (self::TAGS as $title => $color) {
                $this->tagFactory->build($title, $color, $user);
                $manager->persist($this->tagFactory->grabEntity());
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
