<?php
namespace App\Service\Panier;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService {
    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }


    public function add(string $ref)
    {
        // Lors du premier visite dans le magasin, notre panier est vide
        // donc, si je n'ai pas encore de données dans panier, je le met un tableau vide
        $panier = $this->session->get('panier', []);
        if (!empty($panier[$ref])) {
            $panier[$ref]++;
        }else {
            $panier[$ref] = 1;
        }

        //ce set() fait cummuler les données dans la session (pas changer la donnée déjà existée)
        $this->session->set('panier', $panier);
    }

    public function panier_quantity_edit(int $quantity, string $ref)
    {
        $panier = $this->session->get('panier', []);
        $panier[$ref] = $quantity;

        // if (!empty($panier[$id])) {
        //     $panier[$id]++;
        // }else {
        //     $panier[$id] = 1;
        // }

        //ce set() fait cummuler les données dans la session (pas changer la donnée déjà existée)
        $this->session->set('panier', $panier);
    }

    public function remove(string $ref)
    {
        
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$ref])) {
            unset($panier[$ref]);
        }
        $this->session->set('panier', $panier);
    }

    public function removeOne(string $ref)
    {
        // On récupère le panier actuel
        $panier = $this->session->get("panier", []);

        if(!empty($panier[$ref])){
            if($panier[$ref] > 1){
                $panier[$ref]--;
            }else{
                unset($panier[$ref]);
            }
        }

        // On sauvegarde dans la session
        $this->session->set("panier", $panier);
    }

    public function deleteAll()
    {
        $this->session->remove("panier");
    }

    public function getFullcart() : array
    {
        $panier = $this->session->get('panier', []);
        $panierWithData = [];

        foreach ($panier as $ref => $quantity ) {
            $panierWithData[] = [
                'product' => $this->productRepository->findOneBy(['referenceId' => $ref]),
                'quantity' => $quantity
            ];
        }

        return $panierWithData;
    }

    public function getTotal() : float
    {
        $total = 0;
        foreach ($this->getFullcart() as $item ) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }

    public function allQuantityItem() : int
    {
        $panier = $this->session->get('panier', []);

        $allQuantityItem = 0;

        foreach ($panier as $quantity ) {
            $allQuantityItem += $quantity ;
        }

        return $allQuantityItem;
    }


    // FAVORITE CART (in database)

    
    public function addFC(string $ref, $favoriteCart) :array
    {
        // Lors du premier visite dans le magasin, notre panier est vide
        // donc, si je n'ai pas encore de données dans panier, je le met un tableau vide
        $panier = $favoriteCart;
        if (!empty($panier[$ref])) {
            $panier[$ref]++;
        }else {
            $panier[$ref] = 1;
        }
        return $panier;
    }

    public function getFullFavoriteCart($favoriteCart) : array
    {
        $panier = $favoriteCart;

        $panierWithData = [];

        foreach ($panier as $ref => $quantity ) {
            $panierWithData[] = [
                'product' => $this->productRepository->findOneBy(['referenceId' => $ref]),
                'quantity' => $quantity
            ];
        }

        return $panierWithData;
    }

    
    public function getTotalFavoriteCart($favoriteCart) : float
    {
        $total = 0;

        foreach ($this->getFullFavoriteCart($favoriteCart) as $item ) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }

    public function allQuantityItemInFavoriteCart($favoriteCart) : int
    {
        $panier = $favoriteCart;

        $allQuantityItem = 0;

        foreach ($panier as $quantity ) {
            $allQuantityItem += $quantity ;
        }

        return $allQuantityItem;
    }
}