<?php

namespace App\Repository;

use App\Entity\Folder;
use App\Entity\node\AbstractRessource;
use App\Entity\User;
use App\Interface\RessourceInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AbstractRessource>
 */
final class RessourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractRessource::class);
    }

    /**
     * @return RessourceInterface[]
     */
    public function findMainRessourcesForUserByFolder(User $user, ?Folder $parent = null): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->andWhere('r.owner = :user')
            ->andWhere('r NOT INSTANCE OF ' . Folder::class)
            ->setParameter('user', $user)
        ;

        if ($parent) {
            $queryBuilder
                ->andWhere('r.parent = :folder')
                ->setParameter('folder', $parent)
            ;
        } else {
            $queryBuilder
                ->andWhere('r.parent IS NULL')
            ;
        }

        return $queryBuilder
            ->addSelect('CASE WHEN r.updateDate IS NOT NULL THEN r.updateDate ELSE r.creationDate END AS HIDDEN sortDate')
            ->orderBy('sortDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
