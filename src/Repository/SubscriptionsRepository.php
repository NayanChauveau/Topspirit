<?php

namespace App\Repository;

use App\Entity\Subscriptions;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Subscriptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscriptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscriptions[]    findAll()
 * @method Subscriptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscriptions::class);
    }

    /**
     * @return Boolean
     */

    public function doesExistsByPiId($pi)
    {
        return !empty($this->createQueryBuilder('p')
            ->where('p.paymentIntent = :pi')
            ->setParameter('pi', $pi)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult())
        ;
    }

    // /**
    //  * @return Subscriptions[] Returns an array of Subscriptions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Subscriptions
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
