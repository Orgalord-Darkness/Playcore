<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\MaxDepth; 
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection; 
use App\Repository\EditorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EditorRepository::class)]
class Editor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(type: 'integer', message: 'L\'ID doit être un entier.')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[MaxDepth(1)]
    #[Groups(['getEditor', 'createEditor','updateEditor','getVideoGame'])]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    #[Assert\Type(type: 'string', message: 'Le nom doit être une chaîne de caractères.')]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $name;

    #[ORM\Column(length: 255)]
    #[Groups(['getEditor', 'createEditor','updateEditor'])]
    #[Assert\NotBlank(message: "Le pays ne peut pas être vide.")]
    #[Assert\Type(type: 'string', message: 'Le pays doit être une chaîne de caractères.')]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom du pays ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $country;

    #[ORM\OneToMany(targetEntity: VideoGame::class, mappedBy:'editor')]
    #[Groups(['getEditor', 'createEditor','updateEditor'])]
    #[MaxDepth(1)]
    private Collection $videogames;
    
    public function __construct()
    {
        $this->videogames = new ArrayCollection();
    }

    public function getVideoGames(): Collection
    {
        return $this->videogames; 
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }
}
