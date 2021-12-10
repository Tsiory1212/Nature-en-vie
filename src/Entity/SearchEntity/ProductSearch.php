<?php
namespace App\Entity\SearchEntity;

use App\Entity\Category;
use App\Entity\Classement;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


class ProductSearch {

    /**
     * @var string|null
     */
    private $name;

    /**
     *
     * @var Category|null
     */
    private $category;

    /**
     * @var Classement|null
     */
    private $classement;


    /**
     *
     * @var string|null
     */
    private $gamme;

    /**
     * @var int|null
     */
    private $maxPrice;
    
    
    /**
     * Get the value of nom
     *
     * @return  string|null
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of nom
     *
     * @param  string|null  $name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of category
     *
     * @return  Category|null
     */ 
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the value of category
     *
     * @param  Category  $category
     *
     * @return  self
     */ 
    public function setCategory(Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of classement
     *
     * @return  Classement
     */ 
    public function getClassement()
    {
        return $this->classement;
    }

    /**
     * Set the value of classement
     *
     * @param  Classement  $classement
     *
     * @return  self
     */ 
    public function setClassement(Classement $classement)
    {
        $this->classement = $classement;

        return $this;
    }

    /**
     * Get the value of gamme
     *
     * @return  int|null
     */ 
    public function getGamme()
    {
        return $this->gamme;
    }

    /**
     * Set the value of gamme
     *
     * @param  int|null  $gamme
     *
     * @return  self
     */ 
    public function setGamme($gamme)
    {
        $this->gamme = $gamme;

        return $this;
    }

    /**
     * Get the value of maxPrice
     *
     * @return  int|null
     */ 
    public function getMaxPrice()
    {
        return $this->maxPrice;
    }

    /**
     * Set the value of maxPrice
     *
     * @param  int|null  $maxPrice
     *
     * @return  self
     */ 
    public function setMaxPrice($maxPrice)
    {
        $this->maxPrice = $maxPrice;

        return $this;
    }
}