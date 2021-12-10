<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\SearchEntity\ProductSearch;
use App\Form\SearchForm\ProductSearchType;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminProductController extends AbstractController
{
    protected $em;

    protected $repoProduct;

    public function __construct(EntityManagerInterface $em, ProductRepository $repoProduct)
    {
        $this->em = $em;
        $this->repoProduct = $repoProduct;
    }

    
    /**
     * @Route("/admin/produit/liste", name="admin_product_list")
     */
    public function admin_product_list(Request $request, PaginatorInterface $paginator): Response
    {
        $search = new ProductSearch();
        $form = $this->createForm(ProductSearchType::class, $search);
        $form->handleRequest($request);

        $produits = $paginator->paginate(
            $this->repoProduct->findAllVisibleQuery($search),
            $request->query->getInt('page', 1),
            30
        );

        return $this->render('admin/product/list_product.html.twig', [
            'produits'=> $produits,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/product/add", name="admin_product_add")
     */
    public function admin_product_add(Request $request, ProductService $productService): Response
    {
        // On génère un nouveau "referenceId"        
        $lastProduct = $this->repoProduct->findBy(array(),array('id'=>'DESC'),1,0)[0];
        $newRefId = $productService->generateNewRefId($lastProduct);

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
            return $this->redirectToRoute('admin_product_list');
        }

        return $this->render('admin/product/add_product.html.twig', [
            'form' => $form->createView(),
            'newRefId' => $newRefId
        ]);
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
            return $this->redirectToRoute('admin_product_list');
        }
        return $this->render('admin/product/edit_product.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit
        ]);
    }


    /**
     * @Route("/admin/product/{id}/delete ", name="admin_product_delete")
     *
     * @return Response
     */
    public function admin_product_delete(Product $product, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'. $product->getId(), $request->get('_token'))) {
            $this->em->remove($product);
            $this->em->flush();
            $this->addFlash(
                'danger',
                'Produit supprimé'
             );
        }
        return $this->redirectToRoute('admin_product_list');
    }
}
