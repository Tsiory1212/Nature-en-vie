<?php

namespace App\Service;

use Exception;

class Test_curl 
{
    private $apiToken;

    public function __construct()
    {
        $this->apiToken = "3755dd2bea38f92891b19991054d753c53f8bda0bc0f76457920d19c530401f4570e2921";
    }

    /**
     * Permet de recupérer tous les contacts
     * Fitré par liste ($listid)
     */
    public function get_all_contacts(int $listid)
    {      
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://pixelsior.api-us1.com/api/3/contacts?listid=$listid",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
              "Accept: application/json",
              "Api-Token: $this->apiToken"
            ],
          ]);

        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            throw new Exception( curl_error($curl), true);
        }
        curl_close($curl);
        
        $contacts = json_decode($result, true)['contacts'];
        dd($contacts);
        return $contacts;
    }


    /**
     * Permet de créer un contact
     *
     */
    public function create_contact($data)
    {      
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://pixelsior.api-us1.com/api/3/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
              "Accept: application/json",
              "Api-Token: $this->apiToken"
            ],
            CURLOPT_POSTFIELDS => $data
          ]);

        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            throw new Exception( curl_error($curl), true);
        }
        curl_close($curl);
        
        $contact = json_decode($result, true);

        dd($contact);
        return json_decode($result, true);  
        
    }



    /**
     * Permet de créer un contact
     *
     */
    public function update_list_status_for_contact($data)
    {      
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://pixelsior.api-us1.com/api/3/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
              "Accept: application/json",
              "Api-Token: $this->apiToken"
            ],
            CURLOPT_POSTFIELDS => $data
          ]);

        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            throw new Exception( curl_error($curl), true);
        }
        curl_close($curl);
        
        $contact = json_decode($result, true);

        dd($contact);
        return json_decode($result, true);  
        
    }
}