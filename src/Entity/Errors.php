<?php

namespace App\Entity;

use App\Repository\ErrorsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ErrorsRepository::class)
 */
class Errors
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $siren;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiren(): ?int
    {
        return $this->siren;
    }

    public function setSiren(int $siren): self
    {
        $this->siren = $siren;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
