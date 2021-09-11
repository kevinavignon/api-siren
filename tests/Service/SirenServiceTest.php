<?php

namespace App\Tests\Service;

use App\Service\SirenService;
use PHPUnit\Framework\TestCase;

class SirenServiceTest extends TestCase
{
    public function testCheckIfSirenNumberIsValidWhenValidNumericAndLongEnough(): void
    {
        $sirenService = new SirenService();
        $response = $sirenService->checkIfSirenNumberIsValid("123456789");
        $this->assertEquals(true, $response);
    }

    public function testCheckIfSirenNumberIsNotValidWhenNotNumeric(): void
    {
        $sirenService = new SirenService();
        $response = $sirenService->checkIfSirenNumberIsValid("test");
        $this->assertEquals(false, $response);
    }

    public function testCheckIfSirenNumberIsNotValidWhenNotLongEnough(): void
    {
        $sirenService = new SirenService();
        $response = $sirenService->checkIfSirenNumberIsValid("12");
        $this->assertEquals(false, $response);
    }

    public function testCheckIfSirenNumberIsNotValidWhenTooLong(): void
    {
        $sirenService = new SirenService();
        $response = $sirenService->checkIfSirenNumberIsValid("1234567891");
        $this->assertEquals(false, $response);
    }
}
