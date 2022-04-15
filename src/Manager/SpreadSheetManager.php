<?php
namespace App\Manager;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionMethod;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SpreadSheetManager extends AbstractController{
    private $repoProduct;
    private $productService;
    private $em;

    public function __construct(ProductRepository $repoProduct, ProductService $productService, EntityManagerInterface $em)
    {
        $this->repoProduct = $repoProduct;
        $this->productService = $productService;
        $this->em = $em;
    }

    public function readIniFile($name){
        return parse_ini_file($name, true);
    }
    /**
     * Permet d'exécuter l'import CSV
     */
    public function persistImportCsv($file)
    {
        // (1) => On vérifie si aucun fichier n'est séléctionné
        if (empty($file))
        {
             new Response("No file specified", Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
             $this->addFlash(
                'danger',
                'Aucun fichier séléctionné'
             );
             return $this->redirectToRoute('admin_product_excel_import');
        }

        $config = $this->readIniFile('config/product_import.ini');
        $excel_index = $config["excel_index"];

        // (2) => On commmence à ouvrir le fichier à importer
        $fileObject = $file->openFile();
        $fileObject->setFlags(SplFileObject::READ_CSV);
        $fileObject->setCsvControl($separator=";");

        // set All products unavailable
        $this->repoProduct->setAllUnavailable();

        // (3) => On parcour toutes les lignes afin de les enregistrés dans la BDD
        $i = 0;
        foreach  ($fileObject as $key => $row) {
            if($i > 0){

                // On ignore les lignes vides
                if (!array_filter($row)) { 
                    break; 
                } 

                // On vérifie les doublons 
                $row = array_map("utf8_encode", $row);
                $update = true;
                $product = $this->repoProduct->findOneBy(['ref_code' => $row[$excel_index["ref_code"]["index"]]] );
                
                if ($product === null) {
                    $update = false;
                    $product = new Product();
                    $product->setQuantity(1);
                    $product->setReferenceId('P-'.$i);        
                }
                $this->processProduct($product, $row, $config, $update);
                $product->setAvailability(1);
                if($product->getCategoryName() === null || trim($product->getCategoryName()) == ''){
                    $product->setAvailability(0);
                }
                $product->setUpdatedAt(new DateTime());
                if ($product->getPackaging() === null || $product->getPackaging() === 0) {
                    $product->setPackaging(1);
                }
                $product->setPrice(round(($product->getPriceAcnAllier()/$product->getPackaging()) * (1+(floatval($config["tva"])/100)), 2));
                $product->setSourceCsv(json_encode($row));
                $this->em->persist($product);

            }
            $i++;   
        }

        $this->em->flush();
        $this->em->clear();
        $this->addFlash(
            'success',
            'Fichier CSV importé avec succès'
         );
        return $this->redirectToRoute("admin_product_list");
        
    }

    public function processProduct($product, $row, $config, $update=false){
        $excel_index = $config["excel_index"];
        foreach($excel_index as $key => $conf_value){
            if(!$update || ($update && $conf_value["update"] == 1)){
                $value = $row[$conf_value["index"]];
                $type = $conf_value["type"];
                if($type == 'int') $value = intval($value);
                else if ($type == 'float') $value = floatval(str_replace(",", ".", $value));

                $setMethod = new ReflectionMethod(Product::class, $this->getSetMethodName($key));
                $setMethod->invoke($product, $value);
            }
        }
    }

    public function getSetMethodName($key){
        $tab = explode('_', $key);
        $setMethod = "set";
        foreach($tab as $part){
            $setMethod .= strtoupper(substr($part, 0, 1)).substr($part, 1);
        }
        return $setMethod;
    }

        /**
     * Permet d'exécuter l'import CSV
     */
    public function persistImportCsvTsiory($file)
    {
           // (1) => On vérifie si aucun fichier n'est séléctionné
           if (empty($file))
           {
                new Response("No file specified", Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
                $this->addFlash(
                   'danger',
                   'Aucun fichier séléctionné'
                );
                return $this->redirectToRoute('admin_product_excel_import');
           }
   
   
           // (2) => On commmence à ouvrir le fichier à importer
           $fileObject = $file->openFile();
           $fileObject->setFlags(SplFileObject::READ_CSV);
           $fileObject->setCsvControl($separator=";");

   
           // (3) => On parcour toutes les lignes afin de les enregistrés dans la BDD
           $i = 0;
           foreach  ($fileObject as $row) {
               if($i > 0){
                   // On ignore les lignes vides
                   if (!array_filter($row)) { 
                       break; 
                   } 
   
                   
                   // On vérifie les doublons 
                   $row = array_map("utf8_encode", $row);
                   $existingProduct = $this->repoProduct->findOneBy(['ref_code' => $row[0]] );
                   if ($existingProduct === null) {
                        $product = new Product();
                        $product->setRefCode($row[0]);
                        $this->productSetValue($product, $i, $row);
                   }else{
                        $product = $existingProduct;
                        $this->productSetValue($product, $i, $row);
                   }
               }
               $i++;   
           }
   
           $this->em->flush();
           $this->em->clear();

           $this->addFlash(
               'success',
               'Fichier CSV importé avec succès'
            );
           return $this->redirectToRoute("admin_product_list");
    }



    /**
     * Permet d'exécuter l'export de fichier CSV
     */
    public function persistExportCsv()
    {
        // (1) => On récupère tous les produits dans la BDD
        $products = $this->repoProduct->findAll();
        $date = (new \DateTime())->format('Y-m-d');

        // (2) => On crée un fichier CSV
        $separator = ";";
        $file = new SplFileObject("uploads/export.csv", "w");
        
        // (3) => On écrit la première ligne qui sert pour noms des colonnes / Et on gère les caractère spéciaux
        $firstRow = ["Reférence", "Libellé type", "Famille", "Nom", "Description", "Conditionnement", "Prix", "Unité de prix", "Origine production", "Tarif ACN ALLIER"];
        $firstRow = array_map("utf8_decode", $firstRow);

        $file->fputcsv($firstRow, $separator);
        
        // (4) => On parcour tous les prouits dans la BDD et on l'ajoute dans le fichier CSV
        /** @var Product $product */
        foreach($products as $product){
            $cateogry = $this->productService->getNameCategory($product->getCategory());

            $row = [
                $product->getRefCode(), 
                $product->getProductTypeLabel(),
                $cateogry,
                $product->getName(), 
                $product->getDetail(), 
                $product->getPackaging(), 
                str_replace(".", ",", strval($product->getPrice())), 
                $product->getQuantityUnit(), 
                $product->getOriginProduction(), 
                $product->getPriceAcnAllier(), 
            ];

            // On corrige l'encodage des caractères
            $row = array_map("utf8_decode", $row);

            $file->fputcsv($row, $separator);
        }

        return $this->file($file, "produit-$date.csv");
    }


    /**
     * Permet de modifier les valeurs dans un produit
     *
     * @param Produit $product
     * @param int $index
     * @param array $row
     */
    public function productSetValue($product, $index, $row)
    {
        $product->setQuantity(1);
        $product->setReferenceId('P-'.$index);
        $product->setProductTypeLabel($row[1]);
        $product->setCategory($this->productService->getIdCategoryByName($row[2]));
        $product->setName($row[3]);
        $product->setDetail($row[4]);
        
        
        $product->setAvailability(1);
        // $product->setPackaging(intval($row[5]));

        $product->setPackaging(intval($row[5]));
        $product->setPrice($this->productService->dividePriceIfPackagingIsGreatestONE(intval($row[5]), floatval(str_replace(",", ".", $row[10]))));
        $product->setQuantityUnit($row[8]);
        $product->setOriginProduction($row[9]);
        $product->setPriceAcnAllier( floatval(str_replace(",", ".", $row[10])));
        $product->setImageName($row[11]);

        // $product->setQuantityUnit($this->productService->getQuantityUnity($row[5]));
        // $product->setImageName($row[2]);
        // $product->setQuantity(intval($this->productService->getQuantityNumeral($row[5])));
        // $product->setDescription($row[6]);
        // $product->setClassement($this->productService->getIdClasseByName($row[9]));
        // $product->setGamme($this->productService->getIdGammeByName($row[10]));
        // $product->setVolume($row[11]);

        $this->em->persist($product);
    }
}