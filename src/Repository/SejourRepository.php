<?php

namespace App\Repository;

use App\Entity\Sejour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sejour>
 */
class SejourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sejour::class);
    }


    public function findByDateArrivee(\DateTime $date): array
{
    return $this->createQueryBuilder('s')
        ->where('s.dateArrivee = :date')
        ->setParameter('date', $date->format('Y-m-d'))
        ->getQuery()
        ->getResult();
}


public function findByDateAfterArrivee(\DateTime $date)
{
    return $this->createQueryBuilder('s')
        ->andWhere('s.date_arrivee > :date')
        ->setParameter(':date', $date->format('Y-m-d'))
        ->orderBy('s.date_arrivee', 'ASC')
        ->getQuery();
}

    //    /**
    //     * @return Sejour[] Returns an array of Sejour objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sejour
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
