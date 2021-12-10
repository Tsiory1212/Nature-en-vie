<?php

namespace App\Controller;

use App\Service\Test_curl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestCurlController extends AbstractController
{
   /**
     * @Route("/test_curl", name="test_curl")
     */
    public function test_curl(): Response
    {
        return $this->render('CURL/test_curl.html.twig', [
        ]);
    }

       /**
     * @Route("/create_contact", name="create_contact")
     */
    public function create_contact(Test_curl $curl_service): Response
    {
      
        $data =[
            'contact' => [
                'email' => 'Vonjy@example.com',
                'firstName' => 'Taitra',
                'lastName' => 'Vonjy',
                'phone' => '7223224241',
                'note' => '55',
                'fieldValues' => [
                    0 => [
                           'field' => 12,
                           'value' => 'rere',
                       ]
                ],
                // 'field' => 
                //     array (
                //         'type' => 'textarea',
                //         'title' => 'Andrana',
                //         'descript' => 'Andrana Andrana',
                //         'visible' => 1,
                //         'ordernum' => 1,
                //     ),
                // 'subscribe' => [
                //     0 => ['listid' => 11]
                // ]                
            ]
        ];

        $data = json_encode( $data);
     
        $curl_service->create_contact($data);

        return $this->render('CURL/test_curl.html.twig', [

        ]);
    }

    
}
