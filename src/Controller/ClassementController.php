<?php

namespace App\Controller;

use App\Entity\Classement;
use App\Entity\Product;
use App\Form\ClassementType;
use App\Form\ProductType;
use App\Repository\ClassementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassementController extends AbstractController
{
    protected $em;
    protected $repoClasse;

    public function __construct(EntityManagerInterface $em, ClassementRepository $repoClasse)
    {
        $this->em = $em;
        $this->repoClasse = $repoClasse;
    }

    /**
     * @Route("/admin/classe/liste", name="admin_class_list")
     */
    public function admin_class_list(): Response
    {
        $classement = $this->repoClasse->findAll();
        return $this->render('admin/classement/list_classement.html.twig', [
            'classements'=> $classement
        ]);
    }
    
    /**
     * @Route("/admin/classe/add", name="admin_class_add")
     */
    public function admin_class_add(Request $request): Response
    {
        $classe = new Classement();

        $form = $this->createForm(ClassementType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($classe);
            $this->em->flush();
            $this->addFlash(
               'success',
               'Classement ajouté avec succès'
            );
            return $this->redirectToRoute('admin_class_list');
        }
        return $this->render('admin/classement/add_classement.html.twig', [
            'form' => $form->createView(),
        ]);

    }


     /**
     * @Route("/admin/classe/{id}/edit", name="admin_class_edit")
     */
    public function admin_class_edit(Classement $classe, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($classe);
            $this->em->flush();
            $this->addFlash(
               'success',
               'Classement modifié avec succès'
            );
            return $this->redirectToRoute('admin_class_list');
        }
        return $this->render('admin/classement/add_classement.html.twig', [
            'form' => $form->createView(),
            'label' => 'Modification',
            'btn_class' => 'btn-success',
            'button_label' => 'Enregistrer'
        ]);
    }


    /**
     * @Route("/admin/classe/{id}/delete ", name="admin_class_delete")
     *
     * @return Response
     */
    public function admin_class_delete(Classement $classe, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'. $classe->getId(), $request->get('_token'))) {
            $this->em->remove($classe);
            $this->em->flush();
            $this->addFlash(
                'danger',
                'Classement supprimée'
             );
        }
        return $this->redirectToRoute('admin_class_list');
    }

      /**
     * Permet d'ajouter une produit par un catégorie spécifiée
     * 
     * @Route("/admin/classe/{id}/addProduct", name="admin_class_addProduct")
     */
    public function admin_class_addProduct(Classement $classement, Request $request): Response
    {
        $produit = new Product();

        $form = $this->createForm(ProductType::class, $produit)
                        ->remove('classement')
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit->setClassement($classement);

            $this->em->persist($produit);
            $this->em->flush();
            $this->addFlash(
               'success',
               "Produit ajoutée dans la classe {$classement->getName()}"
            );
            return $this->redirectToRoute('admin_class_list');
        }
        return $this->render('admin/classement/addProduct_classement.html.twig', [
            'form' => $form->createView(),
            'classement' => $classement
        ]);
    }
}
