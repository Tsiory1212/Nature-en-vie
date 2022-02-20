<?php

namespace App\Repository;

use App\Entity\SampleDatas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SampleDatas|null find($id, $lockMode = null, $lockVersion = null)
 * @method SampleDatas|null findOneBy(array $criteria, array $orderBy = null)
 * @method SampleDatas[]    findAll()
 * @method SampleDatas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SampleDatasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SampleDatas::class);
    }

    // /**
    //  * @return SampleDatas[] Returns an array of SampleDatas objects
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
    public function findOneBySomeField($value): ?SampleDatas
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
