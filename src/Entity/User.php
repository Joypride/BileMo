<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    #[Groups(["getUsers"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getClients", "getUsers"])]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(["getClients", "getUsers"])]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'users', cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    #[Groups(["getUsers"])]
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
