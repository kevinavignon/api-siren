<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use App\Service\SirenService;
use Symfony\Component\HttpKernel\KernelEvents;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;
use Symfony\Contracts\EventDispatcher\Event;

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
     * @param EventDispatcherInterface $eventDispatcher
     * @return JsonResponse
     */
    public function updateCompanies(EventDispatcherInterface $eventDispatcher): JsonResponse
    {
        //Return error if file not found
        if (!$_FILES || $_FILES['file']['tmp_name'] === ""){
            return new JsonResponse(['response' => 'Fichier manquant'], Response::HTTP_NOT_FOUND);
        }
        // Add file to public folder
        $projectDir = $this->getParameter('kernel.project_dir');
        $targetDir = $projectDir . "/public/files/";
        $file = $_FILES['file']['name'];
        $path = pathinfo($file);
        $filename = $path['filename'];
        $ext = $path['extension'];
        $tempName = $_FILES['file']['tmp_name'];
        $pathFilenameExt = $targetDir.$filename.".".$ext;
        try {
            move_uploaded_file($tempName,$pathFilenameExt);
        }catch(\Exception $e){
            return new JsonResponse(['response' => 'Une erreur est survenue lors de l\'ajout du fichier, veuillez le vérifier et réessayer'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        // dispatch event to process a great number of lines without degrading user experience
        $eventDispatcher->addListener(KernelEvents::TERMINATE, function (Event $event) use ($file) {
            $process = Process::fromShellCommandline($this->getParameter('folder') . 'public/../bin/console update:database ' . $file);
            $process->run();
        });

        return new JsonResponse(['response' => 'Fichier ajouté'], Response::HTTP_OK);
    }
}
