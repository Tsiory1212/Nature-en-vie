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


    public function add(int $id)
    {
        // Lors du premier visite dans le magasin, notre panier est vide
        // donc, si je n'ai pas encore de données dans panier, je le met un tableau vide
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        }else {
            $panier[$id] = 1;
        }

        //ce set() fait cummuler les données dans la session (pas changer la donnée déjà existée)
        $this->session->set('panier', $panier);
    }

    public function addOne(int $id)
    {
         // On récupère le panier actuel
         $panier = $this->session->get("panier", []);

         if(!empty($panier[$id])){
             $panier[$id]++;
         }else{
             $panier[$id] = 1;
         }
 
         // On sauvegarde dans la session
         $this->session->set("panier", $panier);
    }

    public function remove(int $id)
    {
        
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $this->session->set('panier', $panier);
    }

    public function removeOne(int $id)
    {
        // On récupère le panier actuel
        $panier = $this->session->get("panier", []);

        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
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

        foreach ($panier as $id => $quantity ) {
            $panierWithData[] = [
                'product' => $this->productRepository->find($id),
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
}