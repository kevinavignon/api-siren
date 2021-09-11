<?php

namespace App\Service;

class SirenService
{
    public function checkIfSirenNumberIsValid($value): bool
    {
        if (!is_numeric($value)){
            return false;
        }
        if (strlen($value) < 8 || strlen($value) > 9){
            return false;
        }
        return true;
    }
}