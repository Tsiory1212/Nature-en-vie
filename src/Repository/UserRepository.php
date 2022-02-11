<?php

namespace App\Repository;

use App\Entity\SearchEntity\UserSearch;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    
    /**
     *
     * @return Query
     */
    public function findAllQuery(UserSearch $search): Query
    {
        $query = $this->findQuery();

        if ($search->getName()) {
            $query = $query
            ->andwhere('u.lastname LIKE :name')
            ->setParameter('name', '%'.$search->getName().'%');
        }
                
        return $query->getQuery();
    }

    public function findAllEditorQuery(UserSearch $search, $role = "EDITOR")
    {
        $query = $this->createQueryBuilder('e');

        //CAS SI ON AVAIT un entity ROLE, comme relation ManyToMany avec USER
        // $query = $query
        //     ->leftJoin('e.userRoles', 'g')
        //     ->where('g.title IN (:role)')
        //     ->setParameter('role', array('ROLE_EDITOR'));
        // ;

        $query = $query
            /**
             * see =>  https://endelwar.it/2020/08/filter-users-by-role-in-symfony-5/
             */
            ->andWhere('JSON_CONTAINS(e.roles, :role) = 1')
            ->setParameter('role', '"ROLE_' . $role . '"')
        ;   

        if ($search->getName()) {
            $query = $query
                ->andwhere('e.lastname LIKE :lastname')
                ->setParameter('lastname', '%'.$search->getName().'%');
        }
        if ($search->getEmail()) {
            $query = $query
                ->andwhere('e.email LIKE :email')
                ->setParameter('email', '%'.$search->getEmail().'%');
        }
        if ($search->getPhone()) {
            $query = $query
                ->andwhere('e.phone LIKE :phone')
                ->setParameter('phone', '%'.$search->getPhone().'%');
        }

        return $query->getQuery()
            ->getResult()
        ;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    private function findQuery() : QueryBuilder
    {
        return $this->createQueryBuilder('u');
    }
}
