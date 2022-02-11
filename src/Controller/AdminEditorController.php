<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\SearchEntity\BlogSearch;
use App\Entity\SearchEntity\UserSearch;
use App\Entity\User;
use App\Form\BlogSearchType;
use App\Form\BlogType;
use App\Form\SearchForm\UserSearchType;
use App\Form\UserType;
use App\Repository\BlogRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminEditorController extends AbstractController
{
    private $repoEditor;
    private $em;

    public function __construct( UserRepository $repoEditor, EntityManagerInterface $em)
    {
        $this->repoEditor = $repoEditor;
        $this->em = $em;
    }


    /**
     * @Route("/admin/redacteur/liste", name="admin_editor_list")
     * @IsGranted("ROLE_ADMIN")
     */
    public function admin_editor_list(PaginatorInterface $paginator, Request $request): Response
    {

        $search = new UserSearch();
        $form = $this->createForm(UserSearchType::class, $search);
        $form->handleRequest($request);
        
        $editors = $paginator->paginate(
            $this->repoEditor->findAllEditorQuery($search),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/editor/list_editors.html.twig', [
            'editors' => $editors,
            'form' => $form->createView()
         ]);
    }


    /**
     * @Route("/admin/redacteur/add", name="admin_editor_add")
     * 
     * @IsGranted("ROLE_ADMIN")
     * 
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function admin_editor_add( Request $request,  UserPasswordEncoderInterface $encoder)
    {
        $redacteur = new User();
        $form = $this->createForm(UserType::class, $redacteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();

            $hash = $encoder->encodePassword($redacteur, $password);
            $redacteur->setPassword($hash);
            $redacteur->setRoles(["ROLE_EDITOR"]);
   
            $this->em->persist($redacteur);
            $this->em->flush();


            $this->addFlash(
                'success',
                "Un rédacteur a bien été ajouté");
            
            return $this->redirectToRoute('admin_editor_list');
        }

        return $this->render('admin/editor/add.html.twig', [
            'formEditor' => $form->createView()
        ]);
        
    }

    /**
     * Suppression rédacteur
     * 
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/redacteur/{id}/delete", name="admin_editor_delete")
     */
    public function admin_editor_delete(Request $request, User $editor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$editor->getId(), $request->get('_token'))) {
            $this->em->remove($editor);
            $this->em->flush();
        }

        return $this->redirectToRoute('admin_editor_list');
    }

  
     /**
      * @IsGranted("ROLE_ADMIN")
      * @Route("/admin/redacteur/{id}/edit ", name="admin_editor_edit", methods="GET|POST")
      */
      public function admin_editor_edit(User $redacteur, Request $request, UserPasswordEncoderInterface $encoder): Response
      {
  
        $form = $this->createForm(UserType::class, $redacteur);
        $form->handleRequest($request);

          if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $hash = $encoder->encodePassword($redacteur, $password);
            $redacteur->setPassword($hash);

              $this->em->flush();
              $this->addFlash(
                 'success',
                 'Modification avec succès'
              );
              return $this->redirectToRoute('admin_editor_list');
          }

          return $this->render('admin/editor/add.html.twig', [
              'formEditor' => $form->createView(),
          ]);
      }


    /**
     * Permet de lister les articles dans blog
     * 
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/blog/liste", name="admin_blog_list")
     */
    public function admin_blog_list(PaginatorInterface $paginator, BlogRepository $repoBlog, Request $request)
    {        
        $search = new BlogSearch();
        $form = $this->createForm(BlogSearchType::class, $search);
        $form->handleRequest($request);

        $blogs = $paginator->paginate(
            $repoBlog->findAllQuery($search),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/editor/blog/list_blogs.html.twig', [
            'blogs' => $blogs,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'ajouter un article (blog)
     * 
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/blog/add", name="admin_blog_add")
     */
    public function admin_blog_add( Request $request): Response
    {
        $blog = new Blog();

        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($blog);
            $this->em->flush();

            $this->addFlash('success', "Un article a été bien ajouté");

            return $this->redirectToRoute('admin_blog_list');
        }

        return $this->render('admin/editor/blog/add_blog.html.twig', [
            // 'user' => $user,
            'formBlog' => $form->createView(),
            'blog' => $blog
        ]);
    }


    
    /**
     * Suppression article
     * 
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/blog/{id}/delete", name="admin_blog_delete")
     */
    public function admin_blog_delete(Request $request, Blog $blog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->get('_token'))) {
            $this->em->remove($blog);
            $this->em->flush();
            $this->addFlash('danger', 'Suppression de l\'article avec succès');
        }

        return $this->redirectToRoute('admin_blog_list');
    }

    /**
     * Modification article (blog)
     * 
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/blog/{id}/edit ", name="admin_blog_edit", methods="GET|POST")
    */
    public function admin_blog_edit(Blog $blog, Request $request): Response
    {

        $form = $this->createForm(BlogType::class, $blog)
            ->remove('image')
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash(
                'success',
                'Modification de l\'article avec succès'
            );
            return $this->redirectToRoute('admin_blog_list');
        }

        return $this->render('admin/editor/blog/add.html.twig', [
            'formBlog' => $form->createView(),
            'blog' => $blog,
            'button' => 'Enregistrer'
        ]);
    }
    
}
