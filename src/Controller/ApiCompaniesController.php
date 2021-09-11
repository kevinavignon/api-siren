<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Service\SirenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiCompaniesController extends AbstractController
{

    /**
     * @Route("/api/companies/{siren}", name="get_company", methods={"GET"})
     * @param $siren
     * @param CompanyRepository $companyRepository
     * @param SirenService $sirenService
     * @return JsonResponse
     */
    public function getCompany($siren, CompanyRepository $companyRepository, SirenService $sirenService): JsonResponse
    {
        // Check is SIREN number is valid
        if (!$sirenService->checkIfSirenNumberIsValid($siren)){
            return new JsonResponse(['response' => 'Le format du numéro SIREN n\'est pas valide'], Response::HTTP_NOT_FOUND);
        }
        // Get company using SIREN number
        $company = $companyRepository->findOneBy(['siren' => $siren]);
        //If company exist in database, display company informations, else display 404 NOT FOUND
        if ($company){
            return new JsonResponse($company->toArray(), Response::HTTP_OK);
        }
        return new JsonResponse(['response' => 'Ce numéro SIREN n\'est pas attribué'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/api/companies", name="update_companies", methods={"POST"})
     * @param CompanyRepository $companyRepository
     * @return JsonResponse
     */
    public function updateCompanies(CompanyRepository $companyRepository): JsonResponse
    {

        $em = $this->getDoctrine()->getManager();
        //Return error if file not found
        if (!$_FILES || $_FILES['file']['tmp_name'] === ""){
            return new JsonResponse(['response' => 'Fichier manquant'], Response::HTTP_NOT_FOUND);
        }
        // Get post csv file which contains updated companies and convert each CSV line to array
        if (($file = fopen($_FILES['file']['tmp_name'], 'r')) !== FALSE) {
            // Used to verify if this is the first csv file's line
            $isFirstLineFile = true;
            $nbAdd = 0;
            $nbUpdate = 0;
            while (($data = fgetcsv($file, 1000, ";")) !== FALSE) {
                // if is first line, do nothing except change to false in order to execute code for other lines
                if ($isFirstLineFile === true){
                    $isFirstLineFile = false;
                }else{
                    $company = $companyRepository->findOneBy(array('siren' => $data[0]));
                    if (!$company) {
                        $company = new Company();
                        $company->setSiren((int)$data[0]);
                        $company->setCreatedAt(new \DateTime());
                        $em->persist($company);
                        $nbAdd++;
                    }else{
                        $nbUpdate++;
                    }
                    $company->setUpdatedAt(new \DateTime());
                    $company->setName($data[2]);
                    $company->setAddress($data[12]);
                    $company->setCity($data[14]);
                    try{
                        $em->flush();
                    }catch (\Exception $e) {
                        return new JsonResponse(['response' => 'Une erreur est survenue lors de l\'ajout du numéro ' . $data[0] . 'avec l\'erreur : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
            }

            fclose($file);
            return new JsonResponse(['response' => 'Ajout effectué, ' . $nbAdd . ' ajouts et  ' . $nbUpdate . ' mises-à-jour'], Response::HTTP_OK);
        }
        return new JsonResponse(['response' => 'Fichier manquant'], Response::HTTP_NOT_FOUND);
    }
}
