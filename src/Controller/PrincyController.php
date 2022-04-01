<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SampleDatas;
use App\Manager\EntityManager;
use App\Repository\ProductRepository;
use App\Repository\SampleDatasRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ie")
 */
class PrincyController extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="app_my")
     */
    public function index(): Response
    {
        return $this->render('my/index.html.twig', [
            'controller_name' => 'MyController',
        ]);
    }

    /**
     * @Route("/doImport", name="doImport")
     */
    public function import(Request $request, ProductService $productService)
    {
        $file = $request->files->get('myfile');

        if (empty($file))
        {
             new Response("No file specified", Response::HTTP_UNPROCESSABLE_ENTITY, ['content-type' => 'text/plain']);
             return $this->redirectToRoute('admin_princy_import_export', ['no-file']);
        }

        $fileObject = $file->openFile();
        $fileObject->setFlags(SplFileObject::READ_CSV);
        $fileObject->setCsvControl($separator=";");

        $i = 0;
        foreach  ($fileObject as $row) {
            if($i > 0){
                if (!array_filter($row)) { 
                    break; 
                } 

                $sampleDatas = new Product();
                $sampleDatas->setReferenceId('P-'.$i);
                $sampleDatas->setAvailability($productService->valueAvailability($row[0]));
                $sampleDatas->setName($row[1]);
                $sampleDatas->setImageName($row[2]);
                $sampleDatas->setPrice(floatval(str_replace(",", ".", $row[3])));
                $sampleDatas->setWeight(floatval(str_replace(",", ".", $row[4])));
                $sampleDatas->setQuantity(intval($productService->getQuantityNumeral($row[5])));
                $sampleDatas->setDescription($row[6]);
                $sampleDatas->setDetail($row[7]);
                $sampleDatas->setCategory($productService->getIdCategoryByName($row[8]));
                $sampleDatas->setClassement($productService->getIdClasseByName($row[9]));
                $sampleDatas->setGamme($productService->getIdGammeByName($row[10]));
                $sampleDatas->setVolume($row[11]);
                $sampleDatas->setQuantityUnit($productService->getQuantityUnity($row[5]));

                $this->em->persist($sampleDatas);
                $this->em->flush();
            }
            $i++;

            
        }
        return $this->redirectToRoute("admin_product_list");
    }

    // /**
    //  * @Route("/doExport", name="doExport")
    //  */
    // public function export(SampleDatasRepository $sampleDatasRepository){
    //     $sampleDatas = $sampleDatasRepository->findAll();
    //     $separator = ";";
    //     $file = new SplFileObject("export.csv", "w");
    //     $file->fputcsv(["id", "ref", "name", "description", "price", "quantity"], $separator);
    //     foreach($sampleDatas as $sampleData){
    //         $row = [
    //             $sampleData->getId(), 
    //             $sampleData->getRef(), 
    //             $sampleData->getName(), 
    //             $sampleData->getDescription(), 
    //             str_replace(".", ",", strval($sampleData->getPrice())), 
    //             $sampleData->getQuantity()
    //         ];
    //         $file->fputcsv($row, $separator);
    //     }
    //     return $this->file($file, "products.csv");
    // }

    /**
     * @Route("/doExport", name="doExport")
     */
    public function export(ProductRepository $sampleDatasRepository){
        $sampleDatas = $sampleDatasRepository->findAll();
        $separator = ";";
        $file = new SplFileObject("export.csv", "w");
        $file->fputcsv(["id", "name", "price", "weight", "quantity", "detail", "description", "image_name", "updated_at", "category_id", "gamme", "classement_id", "reference_id"], $separator);
        foreach($sampleDatas as $sampleData){
            $row = [
                $sampleData->getId(), 
                $sampleData->getName(), 
                $sampleData->getPrice(), 
                $sampleData->getWeight(), 
                $sampleData->getQuantity(), 
                $sampleData->getDetail(), 
                $sampleData->getDescription(), 
                $sampleData->getImageName(), 
                $sampleData->getUpdatedAt()->format('Y-m-d'),
                $sampleData->getCategory()->getName(), 
                $sampleData->getGammeType(),
                $sampleData->getClassement()->getName(), 
                $sampleData->getReferenceId(), 
                str_replace(".", ",", strval($sampleData->getPrice())), 
                $sampleData->getQuantity()
            ];
            $file->fputcsv($row, $separator);
        }
        return $this->file($file, "products.csv");
    }
}
