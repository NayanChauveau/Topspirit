<?php

namespace App\Repository;

use App\Entity\Platform;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Platform|null find($id, $lockMode = null, $lockVersion = null)
 * @method Platform|null findOneBy(array $criteria, array $orderBy = null)
 * @method Platform[]    findAll()
 * @method Platform[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlatformRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Platform::class);
    }

    /**
     * @return Platform[] Returns an array of Platform objects
     */
    public function findPremiums()
    {
        return $this->createQueryBuilder('p')
            ->where('p.endOfSubscription >= :date')
            ->setParameter('date', new \DateTimeImmutable)
            ->orderBy('p.id', 'ASC') // A changer avec le nombre de visites qu'ils renvoient
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Platform[] Returns an array of Platform objects
     */
    public function findAllPremiumsFirst()
    {
        return array_merge(
            $this->createQueryBuilder('p')
            ->where('p.endOfSubscription >= :date')
            ->setParameter('date', new \DateTimeImmutable)
            ->orderBy('p.id', 'ASC') // A changer avec le nombre de visites qu'ils renvoient
            ->getQuery()
            ->getResult(),
            $this->createQueryBuilder('p')
            ->where('p.endOfSubscription < :date OR p.endOfSubscription is NULL')
            ->setParameter('date', new \DateTimeImmutable)
            ->orderBy('p.id', 'ASC') // A changer avec le nombre de visites qu'ils renvoient
            ->getQuery()
            ->getResult(),
            )
        ;
    }




    // /**
    //  * @return Platform[] Returns an array of Platform objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Platform
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
