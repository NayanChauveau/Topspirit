<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Advertising;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Advertising|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advertising|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advertising[]    findAll()
 * @method Advertising[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertisingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advertising::class);
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

    /**
     * @return \DateTimeImmutable
     */

    public function dateOfDisponibility()
    {
        $lastAdvertising = $this->createQueryBuilder('p')
        ->addOrderBy('p.endingDate', 'DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getResult()
        ;

        if (
            empty($lastAdvertising)
            || $lastAdvertising[0]->getEndingDate() < new \DateTimeImmutable
            ) {
            return new \DateTimeImmutable;
        }

        return $lastAdvertising[0]->getEndingDate()
        ;
    }


    /**
     * @return Advertising[] Returns an array of Advertising objects
     */
    
    public function findActiveByUser(User $user)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :user')
            ->andWhere('a.endingDate > :date')
            ->setParameters(new ArrayCollection([
                new Parameter('user', $user),
                new Parameter('date', new \DateTimeImmutable, Types::DATE_IMMUTABLE)
            ]))
            ->orderBy('a.endingDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    


    // /**
    //  * @return Advertising[] Returns an array of Advertising objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */



    /*
    public function findOneBySomeField($value): ?Advertising
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
