<?php

namespace App\Repository;

use App\Entity\CalendarEvent;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CalendarEvent>
 */
final class CalendarEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalendarEvent::class);
    }

    /**
     * @return CalendarEvent[]
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('ce')
            ->andWhere('ce.owner = :user')
            ->setParameter('user', $user)
            ->orderBy('ce.startTime', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
