<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\PasswordUpdate;
use App\Entity\SearchEntity\BlogSearch;
use App\Form\BlogSearchType;
use App\Form\BlogType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use App\Form\UserType;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class EditorAccountController extends AbstractController
{

    private $em;

    public function __construct( EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Permet de se connecter en tant que rédacteur
     * 
     * @Route("/editor/login", name="editor_login")
     */
    public function editorLogin(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $lastUser = $utils->getLastUsername();

        return $this->render('editor/login.html.twig', [
            'hasError' => $error !== null,
            'lastUser' => $lastUser        
        ]);
    }

    /**
     * Dashboard Admin
     * 
     * @IsGranted("ROLE_EDITOR")
     * @Route("/editor/dashboard", name="editor_dashboard")
     */
    public function editor_dashboard(PaginatorInterface $paginator, BlogRepository $repoBlog, Request $request)
    {        
        $search = new BlogSearch();
        $form = $this->createForm(BlogSearchType::class, $search);
        $form->handleRequest($request);

        $blogs = $paginator->paginate(
            $repoBlog->findAllQuery($search),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('editor/dashboard_editor.html.twig', [
            'blogs' => $blogs,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/editor/profil/edit", name="editor_profil_edit")
     * @IsGranted("ROLE_EDITOR")
     */
    public function editorProfilEdit( Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user)
            ->remove('password')
        ;
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirectToRoute('editor_dashboard');
        }

        return $this->render('editor/account/edit_profil.html.twig', [
            'formUser' => $form->createView(),
        ]);
    }


     /**
     * Permet de modifier le mot de passe
     * 
     * @IsGranted("ROLE_EDITOR")
     * @Route("/editor/account/edit/password", name="editor_password_update")
     *
     */
    public function editorPasswordUpdate(Request $request, UserPasswordEncoderInterface $encoder )
    {
        $user = $this->getUser();
        $passwordUpdate = new PasswordUpdate;

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $passwordUpdate->getNewPassword();
            $hash = $encoder->encodePassword($user, $newPassword);

            $user->setPassword($hash);

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', "Votre mot de passe a été bien modifié");
            
            return $this->redirectToRoute('editor_dashboard');
              
        }
        return $this->render('editor/account/edit_password.html.twig', [
            'formUser' => $form->createView()
        ]);
    }


    /**
     * Permet d'ajouter un article (blog)
     * 
     * @IsGranted("ROLE_EDITOR")
     * @Route("/editor/blog/add", name="editor_blog_add")
     */
    public function editor_blog_add( Request $request): Response
    {
        $blog = new Blog();

        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($blog);
            $this->em->flush();

            $this->addFlash('success', "Un article a été bien ajouté");

            return $this->redirectToRoute('editor_dashboard');
        }

        return $this->render('editor/blog/add_blog.html.twig', [
            // 'user' => $user,
            'formBlog' => $form->createView(),
            'blog' => $blog
        ]);
    }

    /**
     * Suppression article
     * 
     * @IsGranted("ROLE_EDITOR")
     * @Route("/editor/blog/{id}/delete", name="editor_blog_delete")
     */
    public function editorBlogDelete(Request $request, Blog $blog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->get('_token'))) {
            $this->em->remove($blog);
            $this->em->flush();
            $this->addFlash('danger', 'Suppression de l\'article avec succès');
        }

        return $this->redirectToRoute('editor_dashboard');
    }

    /**
     * Modification article (blog)
     * 
     * @IsGranted("ROLE_EDITOR")
     * @Route("/editor/blog/{id}/edit ", name="editor_blog_edit", methods="GET|POST")
    */
    public function editorBlogEdit(Blog $blog, Request $request): Response
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
            return $this->redirectToRoute('editor_dashboard');
        }

        return $this->render('editor/blog/add_blog.html.twig', [
            'formBlog' => $form->createView(),
            'blog' => $blog,
            'button' => 'Enregistrer'
        ]);
    }
}
