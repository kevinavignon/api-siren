<?php

namespace App\Command;

use App\Entity\Company;
use App\Entity\Errors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class UpdateDatabase extends Command
{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    protected $projectDir;

    public function __construct(KernelInterface $kernel, EntityManagerInterface $em)
    {

        parent::__construct();
        $this->projectDir = $kernel->getProjectDir();
        $this->em = $em;
    }

    protected function configure(): void
    {

        $this
            ->setName('update:database')
            ->setDescription('Commande permettant la mise à jour de la BDD Siren')
            ->setHelp('Commande permettant la mise à jour de la BDD Siren')
            ->addArgument('file', InputArgument::REQUIRED, 'tmp fichier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Get post csv file which contains updated companies and convert each CSV line to array
        if (($file = fopen($this->projectDir . '/public/files/' . $input->getArgument('file'), 'r')) !== FALSE) {
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
                            $company->setCreatedAt(new \DateTime());
                            $this->em->persist($company);
                            $nbAdd++;
                        }else{
                            $nbUpdate++;
                        }
                        $company->setUpdatedAt(new \DateTime());
                        $company->setName($data[2]);
                        $company->setAddress($data[12]);
                        $company->setCity($data[14]);
                        try{
                            $this->em->flush();
                        }catch (\Exception $e) {
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
            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }

}

