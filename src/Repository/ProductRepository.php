<?php

namespace App\Repository;

use App\Entity\SearchEntity\ProductSearch;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Permet de récupérer les produits disponibles ou non-disponible
     * @return Query
     */
    public function findAllQuery(ProductSearch $search, $availableProduct = true): Query
    {
        if ($availableProduct === true) {
            $query = $this->findAvailableProductQuery();
        }else {
            $query = $this->createQueryBuilder('p');
        }

        if ($search->getName()) {
            $query = $query
            ->andwhere('p.name LIKE :name')
            ->setParameter('name', '%'.$search->getName().'%');
        }
        if ($search->getCategory()) {
            // dd($search->getCategory());
            
            $query = $query
                ->andwhere("lower(p.category_name) LIKE lower(concat('%',:category,'%')) or lower(:category) LIKE lower(concat('%', p.category_name, '%')) ")
                ->setParameter('category', $search->getCategory()->getName());
        }
        if ($search->getClassement()) {
            // dd($search->getClassement());
            
            $query = $query
                ->andwhere("lower(p.classement_name) LIKE lower(concat('%',:classement,'%')) or lower(:classement) LIKE lower(concat('%',p.classement_name,'%')) ")
                ->setParameter('classement', $search->getClassement()->getRef());
        }
        if ($search->getMaxPrice()) {
            $query = $query
                ->andwhere('p.price <= :maxprice')
                ->setParameter('maxprice', $search->getMaxPrice());
        }

        /**
         * on met la condition $search->getGamme() == '0'
         * Car dans la formulaire de recherche, on utilise getGammeChoices() pour renvoyer un tableau
         * Or getGammeChoices() a un key 0
         * Du coup dans l'url, &gamme=0 signifie gamme=null (???)
         * 
         * A retenir : les valeurs renvoyés dans l'url sont de type "string" mais pas "int"
         */ 
        if ($search->getGamme() || $search->getGamme() == '0') {
            $query = $query
                ->andwhere('p.gamme = :gamme')
                ->setParameter('gamme', $search->getGamme());
        }

        $query = $query->orderBy('p.classement_name', 'DESC')
            ->orderBy('p.name', 'ASC');
            
        return $query->getQuery();
    }

 

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function findByIdCat($catId)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('cat.id = :catId')
            ->setParameter('catId', $catId)
            ->innerJoin('p.category','cat')
            ->getQuery()
            ->getResult()
        ;
    }
    

    public function setAllUnavailable(){
        $q = $this
        ->getEntityManager()
        ->createQuery('update App\Entity\Product p set p.availability = 0');
        $q->execute();
    }


    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    
    private function findAvailableProductQuery() : QueryBuilder
    {
        return $this->createQueryBuilder('p')
        // à revoir 
        ->where('p.availability = true')
        // ->orderBy('p.name', 'ASC')
        ;
    }
}
