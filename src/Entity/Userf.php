<?php

namespace App\Entity;

use App\Repository\UserfRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserfRepository::class)]
class Userf
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

     /**
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column]
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[ORM\Column]
    private $address;

    /**
     * @ORM\Column(type="string", length=20)
     */
    #[ORM\Column]
    private $phone;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }
}
