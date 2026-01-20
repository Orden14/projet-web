<?php

namespace App\Factory;

use App\Entity\User;
use App\Enum\RolesEnum;
use App\Service\User\UserProfileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFactory extends AbstractFactory
{
    public function __construct(
        private readonly UserProfileService $userProfileService,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct($this->entityManager);
    }

    /**
     * Built users are automatically flushed to database.
     */
    public function build(
        string $email,
        string $username,
        string $plainPassword,
        bool $isAdmin = false,
    ): void {
        $user = new User();

        $user->setEmail($email)
            ->setUsername($username)
            ->setPassword($this->passwordHasher->hashPassword($user, $plainPassword))
            ->setRole($isAdmin ? RolesEnum::ADMIN : RolesEnum::USER)
            ->setProfilePicture('preload')
        ;

        $this->entityManager->persist($user);

        $this->userProfileService->setDefaultProfilePicture($user);

        $this->persistEntity();

        $this->entity = $user;
    }

    /**
     * @return User
     */
    public function grabEntity(): object
    {
        return $this->entity ?? new User();
    }
}
