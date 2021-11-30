<?php

namespace App\Repository;

use App\Entity\FactureAbonnement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FactureAbonnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method FactureAbonnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method FactureAbonnement[]    findAll()
 * @method FactureAbonnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureAbonnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FactureAbonnement::class);
    }

    // /**
    //  * @return FactureAbonnement[] Returns an array of FactureAbonnement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FactureAbonnement
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
