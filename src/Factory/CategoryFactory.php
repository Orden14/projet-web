<?php

namespace App\Factory;

use App\Entity\Category;
use App\Entity\User;

final class CategoryFactory extends AbstractFactory
{
    public function build(string $name, User $owner): void
    {
        $category = new Category();

        $category
            ->setName($name)
            ->setOwner($owner)
        ;

        $this->entity = $category;
    }

    /**
     * @return Category
     */
    public function grabEntity(): object
    {
        return $this->entity ?? new Category();
    }
}
