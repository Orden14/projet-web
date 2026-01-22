<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 */
final class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @return Tag[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.owner = :user')
            ->setParameter('user', $user)
            ->orderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
