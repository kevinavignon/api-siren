<?php

namespace App\MessageHandler;

use App\Entity\Company;
use App\Entity\Errors;
use App\Message\UpdateBase;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateBaseHandler implements MessageHandlerInterface

{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(UpdateBase $updateBase)
    {
        // Get post csv file which contains updated companies and convert each CSV line to array
        if (($file = fopen($updateBase->getFolder() . '/public/files/' . $updateBase->getFile(), 'r')) !== FALSE) {
            // Used to verify if this is the first csv file's line
            $isFirstLineFile = true;
            $nbAdd = 0;
            $nbUpdate = 0;
            while (($data = fgetcsv($file, 1000, ";")) !== FALSE) {
                // if is first line, do nothing except change to false in order to execute code for other lines
                if ($isFirstLineFile === true){
                    $isFirstLineFile = false;
                }else{
                    if ($data[0] !== "" and $data[2] !== ""){
                        $company = $this->em->getRepository(Company::class)->findOneBy(array('siren' => $data[0]));
                        if (!$company) {
                            $company = new Company();
                            $company->setSiren((int)$data[0]);
                            $company->setCreatedAt(new DateTime());
                            $this->em->persist($company);
                            $nbAdd++;
                        }else{
                            $nbUpdate++;
                        }
                        $company->setUpdatedAt(new DateTime());
                        $company->setName($data[2]);
                        $company->setAddress($data[12]);
                        $company->setCity($data[14]);
                        try{
                            $this->em->flush();
                        }catch (Exception $e) {
                            $error = new Errors();
                            $error->setSiren((int)$data[0]);
                            $error->setMessage($e->getMessage());
                            $this->em->persist($company);
                            $this->em->flush();
                        }
                    }
                }
            }

            fclose($file);
        }

    }
}