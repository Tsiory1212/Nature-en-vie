<?php

namespace App\Controller\Api;
header("Access-Control-Allow-Origin: *");

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use App\Service\ApiService;
/**
 * @Route("/api/auth", name="api_auth_")
 */
class AuthController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;
    protected $em;
    protected $api;

    public function __construct(EntityManagerInterface $em, Security $security, ApiService $api)
    {
        $this->em = $em;
        $this->security = $security;
        $this->api = $api;
    }
     /**
     * @Route("/signup", name="signup", methods={"POST"})
     */
    public function signup(Request $request, UserPasswordEncoderInterface $encoder): JsonResponse
    {
        try{
            $parameters = json_decode($request->getContent(), true);
            $user = new User();

            $user->setLastname($parameters['lastname']);
            $user->setFirstname($parameters['firstname']);
            $user->setEmail($parameters['email']);
            $user->setPhone($parameters['phone']);
            $user->setPassword($parameters['password']);

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $this->em->persist($user);
            $this->em->flush();

            $payload = [
                "userId"=> $user->getId()
            ];
            $jwt_secret = $this->getParameter('jwt_secret');
            $jwt = $this->api->encode($payload, $jwt_secret);

            $res = [
                'user'  => $user->getUserIdentifier(),
                'token' =>  $jwt,
            ];
            return $this->api->success("Signup successful", $res);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }

    /**
     * 
     * @Route("/login", name="login", methods={"POST"})
     *
     */
    public function login(AuthenticationUtils $authenticationUtils, TokenStorageInterface $tokenStorage): Response
    {
        try{
            $user = $this->security->getUser(); // Getting the request.body
            $error = $authenticationUtils->getLastAuthenticationError();
            if (null === $user) throw new \Exception("Compte invalide.", Response::HTTP_UNAUTHORIZED);
            if(isset($error)) throw new \Exception($error->error, Response::HTTP_UNAUTHORIZED);
            
            $payload = [
                "userId"=> $user->getId()
            ];
            $jwt_secret = $this->getParameter('jwt_secret');
            $jwt = $this->api->encode($payload, $jwt_secret);

            $res = [
                'user'  => $user->getUserIdentifier(),
                'token' =>  $jwt,
            ];
            return $this->api->success("Login successful", $res);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
        
      }

    /**
     * 
     * @Route("/check", name="check", methods={"GET"})
     *
     */
    public function check(Request $request): Response
    {
        try{
            $bearer = $request->headers->get('Authorization');
            $jwt_secret = $this->getParameter('jwt_secret');
            $payload = $this->api->decode($bearer, $jwt_secret);

            return $this->api->success("Check successful", $payload);
        }
        catch(\Exception $error){
            return $this->api->response($error->getCode(), $error->getMessage());
        }
    }

    
}
