<?php

namespace App\Repository;

use App\Entity\Folder;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Folder>
 */
final class FolderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Folder::class);
    }

    /**
     * @return Folder[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.owner = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Folder[]
     */
    public function findInsideFolderByUser(User $user, ?Folder $parent = null): array
    {
        $queryBuilder = $this->createQueryBuilder('f')
            ->andWhere('f.owner = :user')
            ->setParameter('user', $user)
        ;

        if ($parent) {
            $queryBuilder
                ->andWhere('f.parent = :parent')
                ->setParameter('parent', $parent)
            ;
        } else {
            $queryBuilder
                ->andWhere('f.parent IS NULL')
            ;
        }

        return $queryBuilder
            ->orderBy('f.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
