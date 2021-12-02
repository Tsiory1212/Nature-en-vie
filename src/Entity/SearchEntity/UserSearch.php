<?php
namespace App\Entity\SearchEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


class UserSearch {

    /**
     * @var int|null
     */
    private $name;
    
    /**
     * Get the value of nom
     *
     * @return  int|null
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of nom
     *
     * @param  int|null  $nom
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}