<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ClassementRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;

class ProductService 
{
    /** @var CategoryRepository */
    protected $repoCategory;

    /** @var ClassementRepository */
    protected $repoClass;

    protected $repoProduct;

    public function __construct(CategoryRepository $repoCategory, ClassementRepository $repoClass, ProductRepository $repoProduct)
    {
        $this->repoCategory = $repoCategory;
        $this->repoClass = $repoClass;
        $this->repoProduct = $repoProduct;
    }

    public function generateNewRefId()
    {
        $products = $this->repoProduct->findBy([], ['id'=>'DESC'],1,0);
        if (empty($products)) {
            return $newRefId = 'P-1';
        }

        $lastProduct = $products[0];

        // 1- On recupère sa ReferenceID
        $RefIdProduct = $lastProduct->getreferenceId();

        // 2- On prend juste la partie Chiffre (car ref est sous la forme de P-452)
        $getNumberOnly = substr($RefIdProduct, 2); 
        
        // 3- On le converti en entier
        $toInt = (int) $getNumberOnly;
        
        // 4- On l'incrémente de 1
        $toIncremente = $toInt + 1;
        
        // 5- On ajoute le préfix P-
        $newRefId = "P-".$toIncremente;

        return $newRefId;
    }


    /**
     * Permet d'identifier l'id de la catégorie par son nom
     * Si la clé est introuvable, sa valeur devient "Null"
     *
     * @param string $name
     */
    public function getIdCategoryByName($name)
    {
        $categories = $this->repoCategory->findAll();
        $newCategories = [];
        foreach ($categories as $category) {
           $newCategories[$category->getName()] = $category->getId();
        }
    
        if (isset($newCategories[$name])) {
            return $this->repoCategory->find($newCategories[$name]);
        }else{
            return null;
        }
    }

    /**
     * Permet de récupérer le nom de la catégorie d'un produit
     * Nb : La fonction renvoie null, si elle ne trouve aucune catégorie
     * @return string
     */
    public function getNameCategory($category)
    {
        if ($category == null) {
            return null;
        } else {
            return $category->getName();
        }
    }

    
    /**
     * Permet d'identifier l'id du classement par son nom
     * Si la clé est introuvable, sa valeur devient "Null"
     *
     * @param string $name
     */
    public function getIdClasseByName($name)
    {
        $classes = $this->repoClass->findAll();
        $newClasses = [];
        foreach ($classes as $class) {
           $newClasses[$class->getName()] = $class->getId();
        }
    
        if (isset($newClasses[$name])) {
            return $this->repoClass->find($newClasses[$name]);
        }else{
            return null;
        }
    }


    public function getIdGammeByName($name)
    {
        $gammes = Product::GAMME;
        $newGammes = [];
        foreach ($gammes as $key => $value) {
            $newGammes[$value] = $key;
        }
        
        if (isset($newGammes[$name])) {
            return $newGammes[$name];
        }else{
            return null;
        }
    }
    

    public function valueAvailability($value)
    {
        if ($value === "VRAI") {
            return true;
        }elseif($value === "FAUX"){
            return false;
        }
    }
 
    public function getQuantityNumeral($plainText)
    {
        $arrayExpl = explode(' ', $plainText);
        // if (gettype($arrayExpl[0]) == 'string') {
        //     return null;
        // }
        return $arrayExpl[0];
    }

    public function getQuantityUnity($plainText)
    {
        $arrayExpl = explode(' ', $plainText);
        return $arrayExpl[1] ?? 'null';
    }


    public function dividePriceIfPackagingIsGreatestONE($packaging, $pricePackaging)
    {

        // Le conditionnement devient 1 si le champ est vide ou null
        if ($packaging === null || $packaging === '') {
            $packaging = 1;
        }
        
        // On renvoye la valeur du prix si le conditionnement est 1
        if ($packaging == 1) {
            $newprice = $pricePackaging;
            return $newprice;
        }else if($packaging > 1){
            $calculPrice = $pricePackaging/$packaging;

            // Si la division entre le prix et le conditionnement donne un nombre inférieur à 1, on NE fait PAS l'arrondissement
            if ($calculPrice < 1) {
                $calculPrice = round($calculPrice, 2);
                $newprice = $calculPrice ;

            }else{
                $newprice = round($calculPrice, 2) ;
            }

            return $newprice;
        }else if($packaging < 1){
            return $pricePackaging;
        }

        
    }
}