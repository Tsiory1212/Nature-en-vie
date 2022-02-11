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
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $phone;

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

     /**
     * Get the value of email
     *
     * @return  string|null
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param  string|null  $email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of phone
     *
     * @return  string|null
     */ 
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @param  string|null  $phone
     *
     * @return  self
     */ 
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }
}