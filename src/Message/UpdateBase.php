<?php

namespace App\Message;

class UpdateBase
{
    private $file;
    private $folder;

    public function __construct(string $file, string $folder)
    {
        $this->file = $file;
        $this->folder = $folder;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

}