<?php

namespace App\Repository;

use App\Entity\CalendarEvent;
use App\Entity\User;
use DateTimeInterface;
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

    /**
     * @return CalendarEvent[]
     */
    public function findByPeriodForUser(User $user, DateTimeInterface $start, DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('ce')
            ->andWhere('ce.owner = :user')
            ->andWhere('ce.startDate BETWEEN :start AND :end OR ce.endDate BETWEEN :start AND :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->orderBy('ce.startDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
