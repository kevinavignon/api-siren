<?php
// tests/Command/UpdateDatabaseCommandTest.php
namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateDatabaseCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('update:database');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'file' => 'file.csv'
        ]);
        $output = $commandTester->getDisplay();
        $this->assertEquals("", $output);
    }
}