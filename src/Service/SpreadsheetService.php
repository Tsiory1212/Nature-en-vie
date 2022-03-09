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

    /**
     * Permet d'importer un fichier Excel vers la base de données en fonction du nom de la classe
     *
     */
    public function importFileExcel($entityClass)
    {
        $classMetadata = $this->em->getClassMetadata($entityClass);
        $tableName = $classMetadata->getTableName();
        
        if ($_FILES["import_excel"]["name"]!= '' ) {
            $allowed_extension = array('xls', 'csv', 'xlsx', 'ods');
            $file_array = explode(".", $_FILES["import_excel"]["name"]);
            $file_extension = end($file_array);

            // On vérifie si on a le bon format
            if (in_array($file_extension, $allowed_extension)) {
                $file_type = IOFactory::identify($_FILES["import_excel"]["tmp_name"]);
                $reader = IOFactory::createReader($file_type);     
                $spreadsheet = $reader->load($_FILES["import_excel"]["tmp_name"]);
                $data = $spreadsheet->getActivesheet()->toArray();
                
                // On enlève la première ligne dans Excel qui sert pour nom de champ
                unset($data[0]);


                $items = $this->em->getRepository($entityClass)->findAll();
                // On vérifie les doublons (par rapport à l'id)
                foreach($data as $row){
                    $insert_data = [':id' => $row[0] ];
                    
                    foreach ($items as $item) {
                        $itemId = $item->getId(); 
                        if ($insert_data[':id'] == $itemId) {
                            unset($data[$insert_data[':id']]);
                        }
                    }
                }
                $newData = $data;

                // On met dans un tableau, tous les noms de champ de la table $tableName
                $schemaManager = $this->em->getConnection()->getSchemaManager();
                $columns = $schemaManager->listTableColumns($tableName);
                $columnNames = [];
                foreach($columns as $column){
                    $columnNames[] = $column->getName();
                }
                foreach($columns as $column){
                    $columnNames2[] = ':'.$column->getName();
                }

                $implodeTablesQuery = implode(", ",$columnNames); 
                $implodeTablesQuery2 = implode(", ",$columnNames2); 

                foreach($newData as $row){
                    for ($i=0; $i < count($columnNames); $i++) { 
                        $insert_data[':'.$columnNames[$i]] = $row[$i];
                    }
                    /* Output : $insert_data */
                    // $insert_data = [
                    //     ':id' => $row[0],
                    //     ':name' => $row[1],
                    //     ':description' => $row[2],
                    //     etc...
                    // ];

                    
                    $query = " INSERT INTO $tableName ($implodeTablesQuery) VALUES ($implodeTablesQuery2) ";
                    // $query = " INSERT INTO product (id, ref, description ) VALUES (:id, :ref, :description) ";
                    $connexion = $this->em->getConnection();
                    $statement = $connexion->prepare($query);
                    $statement->execute($insert_data);
                }
                
                $message = '<div class="alert alert-success text-center">Data Imported successfully </div>';
            }else{
                $message = '<div class="alert alert-danger text-center">Only .xls .csv or .xlsx file allowed</div>';
            }
        }else{
            $message = '<div class="alert alert-danger text-center">Please Select File</div>';
        }
        return $message; 
    }
} 