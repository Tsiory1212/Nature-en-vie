<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoryController extends AbstractController
{
    private $em;
    private $repoCategory;
    private $repoProduct;

    public function __construct(EntityManagerInterface $em, CategoryRepository $repoCategory, ProductRepository $repoProduct)
    {
        $this->em = $em;
        $this->repoCategory = $repoCategory;
        $this->repoProduct = $repoProduct;
    }

    /**
     * @Route("/admin/category/liste", name="admin_category_list")
     */
    public function admin_category_list(): Response
    {
        $categories = $this->repoCategory->findAll();
        return $this->render('admin/category/list_category.html.twig', [
            'categories'=> $categories
        ]);
    }
    
    /**
     * @Route("/admin/category/add", name="admin_category_add")
     */
    public function admin_category_add(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash(
               'success',
               'Categorie ajoutée avec succès'
            );
            return $this->redirectToRoute('admin_category_list');
        }
        return $this->render('admin/category/add_category.html.twig', [
            'form' => $form->createView(),
        ]);

    }


     /**
     * @Route("/admin/category/{id}/edit", name="admin_category_edit")
     */
    public function admin_category_edit(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash(
               'success',
               'Catégorie modifiée avec succès'
            );
            return $this->redirectToRoute('admin_category_list');
        }
        return $this->render('admin/category/add_category.html.twig', [
            'form' => $form->createView(),
            'label' => 'Modification',
            'btn_class' => 'btn-success',
            'button_label' => 'Enregistrer'
        ]);
    }


    /**
     * @Route("/admin/category/{id}/delete ", name="admin_category_delete")
     *
     * @return Response
     */
    public function admin_category_delete(Category $category, Request $request, ProductRepository $repoProduct): Response
    {
        if ($this->isCsrfTokenValid('delete'. $category->getId(), $request->get('_token'))) {
            
            // (1) On change la catégorie des produits à supprimer pour éviter de les supprimer car il y a la notion de relativité
            $productsRelatedCat = $repoProduct->findByIdCat($category->getId());
            $otherCat = $this->repoCategory->findOneBy(['name' => 'Autre']); 
            foreach ($productsRelatedCat as $product) {
                $product->setCategory($otherCat);
                $this->em->persist($product);
                $this->em->flush();
            }
            
            // (2) On Supprime maintenant la catégorie
            $this->em->remove($category);
            $this->em->flush();
            $this->addFlash(
                'danger',
                'Catégorie supprimée'
             );
        }
        return $this->redirectToRoute('admin_category_list');
    }

    /**
     * Permet d'ajouter une produit par un catégorie spécifiée
     * 
     * @Route("/admin/category/{id}/addProduct", name="admin_category_addProduct")
     */
    public function admin_category_addProduct(Category $category, Request $request, ProductService $productService): Response
    {
        // On génère un nouveau "referenceId"        
        $lastProduct = $this->repoProduct->findBy(array(),array('id'=>'DESC'),1,0)[0];
        $newRefId = $productService->generateNewRefId($lastProduct);
        
        $produit = new Product();

        $form = $this->createForm(ProductType::class, $produit)
                        ->remove('category')
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit->setCategory($category);

            $this->em->persist($produit);
            $this->em->flush();
            $this->addFlash(
               'success',
               "Produit ajoutée dans catégorie {$category->getName()}"
            );
            return $this->redirectToRoute('admin_category_list');
        }
        return $this->render('admin/category/addProduct_category.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'newRefId' => $newRefId
        ]);
    }
}
