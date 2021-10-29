<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

 /**
     * Permet de se connecter en tant qu'administrateur
     * 
     * @Route("/admin/login", name="admin_account_login")
     */
    public function admin_login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $lastUser = $utils->getLastUsername();

        return $this->render('admin/login.html.twig', [
            'hasError' => $error !== null,
            'lastUser' => $lastUser        
        ]);
    }
    
     /**
     * insciption
     * 
     * @Route("/inscription", name="inscription", methods={"GET", "POST"})
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('security_login');
        }
        return $this->render('security/registration.html.twig', [
            'formReg' => $form->createView(),
        ]);
    }

    /**
     * 
     * @Route("/login", name="security_login")
     *
     */
    public function security_login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $lastUser = $utils->getLastUsername();
        
        return $this->render('security/login.html.twig', [
            'hasError' => $error !== null,
            'lastUser' => $lastUser
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function security_logout()
    {
    }
}
