<?php

namespace App\Factory;

use App\Entity\Tag;
use App\Entity\User;

final class TagFactory extends AbstractFactory
{
    public function build(string $title, string $color, User $owner): void
    {
        $tag = new Tag();

        $tag
            ->setTitle($title)
            ->setColor($color)
            ->setOwner($owner)
        ;

        $this->entity = $tag;
    }

    /**
     * @return Tag
     */
    public function grabEntity(): object
    {
        return $this->entity ?? new Tag();
    }
}
