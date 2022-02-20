<?php

namespace App\Service;

use App\Repository\ImmoRepository;
use App\Repository\ProductRepository;
use App\Repository\SampleDatasRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SpreadsheetService
{
    private $em;
    protected $repopProduct;
    protected $repoImmo;

    public function __construct(EntityManagerInterface $em, ProductRepository $repopProduct, SampleDatasRepository $repoImmo)
    {
        $this->em = $em;
        $this->repopProduct = $repopProduct;
        $this->repoImmo = $repoImmo;
    }


    public function importFileExcel()
    {
        
        if ($_FILES["import_excel"]["name"]!= '' ) {
            $allowed_extension = array('xls', 'csv', 'xlsx', 'ods');
            $file_array = explode(".", $_FILES["import_excel"]["name"]);
            $file_extension = end($file_array);

            if (in_array($file_extension, $allowed_extension)) {
                $file_type = IOFactory::identify($_FILES["import_excel"]["tmp_name"]);
                $reader = IOFactory::createReader($file_type);     
                $spreadsheet = $reader->load($_FILES["import_excel"]["tmp_name"]);
                $data = $spreadsheet->getActivesheet()->toArray();
                
                // On enlève la première ligne dans Excel qui sert pour nom de champ
                unset($data[0]);

                $products = $this->repoImmo->findAll();
                // $insert_data = [];

                // On vérifie les doublons
                foreach($data as $row){
                    $insert_data = [':id' => $row[0] ];
                    
                    foreach ($products as $item) {
                        $itemId = $item->getId(); 
                        if ($insert_data[':id'] == $itemId) {
                            unset($data[$insert_data[':id']]);
                        }
                    }
                }
                $newData = $data;


                foreach($newData as $row){
                    $insert_data = [
                        ':id' => $row[0],
                        ':ref' => $row[1],
                        ':name' => $row[2],
                        ':description' => $row[3]
                    ];
                    $query = "
                        INSERT INTO sample_datas (id, ref, name, description)
                        VALUES (:id, :ref, :name, :description)
                    ";
    
                    $connexion = $this->em->getConnection();
                    $statement = $connexion->prepare($query);
                    $statement->execute($insert_data);
                }
                
                $message = '<div class="alert alert-success">Data Imported successfully </div>';
            }else{
                $message = '<div class="alert alert-danger">Only .xls .csv or .xlsx file allowed</div>';
            }
        }else{
            $message = '<div class="alert alert-danger">Please Select File</div>';
        }
        return $message; 
    }
} 