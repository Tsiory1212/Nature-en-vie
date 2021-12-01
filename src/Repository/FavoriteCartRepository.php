<?php

namespace App\Repository;

use App\Entity\FavoriteCart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FavoriteCart|null find($id, $lockMode = null, $lockVersion = null)
 * @method FavoriteCart|null findOneBy(array $criteria, array $orderBy = null)
 * @method FavoriteCart[]    findAll()
 * @method FavoriteCart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavoriteCartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteCart::class);
    }

    // /**
    //  * @return FavoriteCart[] Returns an array of FavoriteCart objects
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
    public function findOneBySomeField($value): ?FavoriteCart
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
