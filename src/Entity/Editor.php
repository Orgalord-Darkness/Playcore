<?php

namespace App\Entity;

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
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getEditor', 'createEditor','updateEditor','getVideoGame'])]
    private ?string $name;

    #[ORM\Column(length: 255)]
    #[Groups(['getEditor', 'createEditor','updateEditor'])]
    private ?string $country;

    #[ORM\OneToMany(targetEntity: VideoGame::class, mappedBy:'editor')]
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
