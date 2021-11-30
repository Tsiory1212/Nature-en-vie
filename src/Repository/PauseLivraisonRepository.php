<?php

namespace App\Repository;

use App\Entity\PauseLivraison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PauseLivraison|null find($id, $lockMode = null, $lockVersion = null)
 * @method PauseLivraison|null findOneBy(array $criteria, array $orderBy = null)
 * @method PauseLivraison[]    findAll()
 * @method PauseLivraison[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PauseLivraisonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PauseLivraison::class);
    }

    // /**
    //  * @return PauseLivraison[] Returns an array of PauseLivraison objects
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
    public function findOneBySomeField($value): ?PauseLivraison
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
