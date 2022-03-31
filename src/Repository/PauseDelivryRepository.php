<?php

namespace App\Repository;

use App\Entity\PauseDelivry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PauseDelivry|null find($id, $lockMode = null, $lockVersion = null)
 * @method PauseDelivry|null findOneBy(array $criteria, array $orderBy = null)
 * @method PauseDelivry[]    findAll()
 * @method PauseDelivry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PauseDelivryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PauseDelivry::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PauseDelivry $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(PauseDelivry $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return PauseDelivry[] Returns an array of PauseDelivry objects
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
    public function findOneBySomeField($value): ?PauseDelivry
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
