<?php
namespace App\Entity\SearchEntity;
use Symfony\Component\Validator\Constraints as Assert;


class BlogSearch {

    /**
     * @var string|null
     */
    private $title;
    

    /**
     * Get the value of title
     *
     * @return  string|null
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param  string|null  $title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}