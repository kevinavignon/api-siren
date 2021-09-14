<?php

namespace App\Service;

use Doctrine\DBAL\Driver\Exception;

class SaveToFolder
{
    public function saveToFolder($file, $projectDir): bool
    {
        $targetDir = $projectDir . "/public/files/";
        $path = pathinfo($file["name"]);
        $filename = $path['filename'];
        $ext = $path['extension'];
        $tempName = $file['tmp_name'];
        $pathFilenameExt = $targetDir.$filename.".".$ext;
        try {
            move_uploaded_file($tempName, $pathFilenameExt);
        }catch(Exception $e){
            return false;
        }
        return true;
    }
}