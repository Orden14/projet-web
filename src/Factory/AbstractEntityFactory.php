<?php

namespace App\Factory;

use App\Interface\FactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractEntityFactory implements FactoryInterface
{
    protected ?object $entity = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    final public function persistEntity(): void
    {
        if ($this->entity === null) {
            return;
        }

        $this->entityManager->persist($this->entity);
        $this->entityManager->flush();
    }
}
