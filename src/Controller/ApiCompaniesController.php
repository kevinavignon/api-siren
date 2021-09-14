<?php

namespace App\Controller;

use App\Message\UpdateBase;
use App\Repository\CompanyRepository;
use App\Service\SaveToFolder;
use App\Service\SirenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param SaveToFolder $folder
     * @return JsonResponse
     */
    public function updateCompanies(SaveToFolder $folder): JsonResponse
    {
        //Return error if file not found
        if (!$_FILES || $_FILES['file']['tmp_name'] === ""){
            return new JsonResponse(['response' => 'Fichier manquant'], Response::HTTP_NOT_FOUND);
        }
        $file = $_FILES["file"];
        // Add file to public folder
        if (!$folder->saveToFolder($file, $this->getParameter('kernel.project_dir'))){
            return new JsonResponse(['response' => 'Une erreur est survenue lors de l\'ajout du fichier, veuillez le vérifier et réessayer'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // dispatch event to process a great number of lines without degrading user experience
        $this->dispatchMessage(new UpdateBase($file['name'], $this->getParameter('folder')));

        return new JsonResponse(['response' => 'Fichier ajouté'], Response::HTTP_OK);
    }
}
