<?php

namespace App\Repository;

use App\Entity\CartSubscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CartSubscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartSubscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartSubscription[]    findAll()
 * @method CartSubscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartSubscription::class);
    }

    // /**
    //  * @return CartSubscription[] Returns an array of CartSubscription objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CartSubscription
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
