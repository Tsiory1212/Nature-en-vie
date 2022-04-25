<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\JsonResponse;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\Security\Core\Security;

class ApiService
{   
    public function __construct ()
    {
    }
    public function success($message, $data) : JsonResponse
    {
       return new JsonResponse([
           "META"=>[
               "status"=>200,
               "message"=>$message
           ],
           "DATA"=> $data
        ]);
    }
    public function error($message, $data = null) : JsonResponse
    {
       return new JsonResponse([
           "META"=>[
               "status"=>500,
               "message"=>$message
           ],
           "DATA"=> $data
        ]);
    }
    public function response($status, $message, $data = null) : JsonResponse
    {
       return new JsonResponse([
           "META"=>[
               "status"=>$status == 0 ? 500 : $status,
               "message"=>$message
           ],
           "DATA"=> $data
        ]);
    }
    public function encode($payload, $jwt_secret){
        $jwt = JWT::encode($payload, $jwt_secret,'HS256');
        return $jwt;
    }
    public function decode($bearer, $jwt_secret){
        $tmp = explode(' ', $bearer);
        if(!isset($tmp[1])) return null;
        $jwt = $tmp[1];
        $payload = JWT::decode($jwt, new Key($jwt_secret, 'HS256'));
        return $payload;
    }
}