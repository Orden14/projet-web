<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\Enum\RolesEnum;
use App\Tests\Unit\Entity\Abstraction\AbstractEntityTest;
use Override;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserTest extends AbstractEntityTest
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function _before(): void
    {
        $this->userPasswordHasher = $this->tester->grabService(UserPasswordHasherInterface::class);
    }

    /**
     * @return User
     */
    #[Override]
    public function _generateEntity(): object
    {
        $user = new User();

        $user->setEmail('testEmail@test.fr')
            ->setUsername('testUsername')
            ->setRole(RolesEnum::USER)
            ->setPassword($this->userPasswordHasher->hashPassword($user, 'testPassword123'))
        ;

        return $user;
    }

    /**
     * @param User $generatedEntity
     */
    public function _testBasicPropertiesOf(mixed $generatedEntity): void
    {
        $this->tester->assertInstanceOf(User::class, $generatedEntity);
        $this->tester->assertEquals('testEmail@test.fr', $generatedEntity->getEmail());
        $this->tester->assertEquals('testUsername', $generatedEntity->getUsername());
        $this->tester->assertEquals(RolesEnum::USER, $generatedEntity->getRole());
        $this->tester->assertTrue($this->userPasswordHasher->isPasswordValid($generatedEntity, 'testPassword123'));
    }

    /**
     * @param User $generatedEntity
     */
    #[Override]
    public function _testRelationalPropertiesOf(mixed $generatedEntity): void
    {
        // No relational properties to test
    }

    #[Override]
    public function _expectedAssertionsErrorCount(): int
    {
        return 0;
    }
}
