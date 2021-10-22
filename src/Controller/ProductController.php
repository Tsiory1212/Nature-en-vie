<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    /**
     * @Route("/admin/product/add", name="admin_product_add")
     */
    public function admin_product_add(Request $request): Response
    {
        $produit = new Product();

        $form = $this->createForm(ProductType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($produit);
            $this->em->flush();
            $this->addFlash(
               'success',
               'Produit ajouté avec succès'
            );
            return $this->redirectToRoute('admin_dashboard');
        }
        return $this->render('admin/product/add_product.html.twig', [
            'form' => $form->createView(),
        ]);

        return $this->render('admin/product/add_product.html.twig', []);
    }


     /**
     * @Route("/admin/product/{id}/edit", name="admin_product_edit")
     */
    public function admin_product_edit(Product $produit, Request $request): Response
    {
        $form = $this->createForm(ProductType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($produit);
            $this->em->flush();
            $this->addFlash(
               'success',
               'Produit modifié avec succès'
            );
            return $this->redirectToRoute('admin_dashboard');
        }
        return $this->render('admin/product/edit_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/admin/product/{id}/delete ", name="admin_product_delete")
     *
     * @return Response
     */
    public function admin_product_delete(Product $product, Request $request): Response
    {
        // Pour la sécurité, on doit vérifier le csrf pour qu'on evite les attaques injections
        //pour éviter aussi qu'un utilisateur mal intentinné rentre l'url à la main
        if ($this->isCsrfTokenValid('delete'. $product->getId(), $request->get('_token'))) {
            $this->em->remove($product);
            $this->em->flush();
            $this->addFlash(
                'danger',
                'Produit supprimé'
             );
        }
        return $this->redirectToRoute('admin_dashboard');
    }
}
