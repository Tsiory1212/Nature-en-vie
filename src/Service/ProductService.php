<?php

namespace App\Service;

use App\Entity\Product;

class ProductService 
{
    public function generateNewRefId(Product $product)
    {
        // 1- On recupère sa ReferenceID
        $RefIdProduct = $product->getreferenceId();

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
}