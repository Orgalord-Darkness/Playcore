<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection; 
use Doctrine\Common\Collections\Collection;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\VideoGame;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\Type(type: 'integer', message: 'L\'ID doit être un entier.')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getCategory', 'createCategory', 'updateCategory','createVideoGame','updateVideoGame'])]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    #[Assert\Type(type: 'string', message: 'Le nom doit être une chaîne de caractères.')]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom ne peut pas dépasser 255 caractères."
    )]
    #[MaxDepth(1)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: VideoGame::class, mappedBy: 'categories')]
    #[Assert\Type(
        type: Collection::class,
        message: 'La propriété videoGames doit être une collection.'
    )]
    #[Assert\Valid]
    #[MaxDepth(1)]
    #[Groups(['getCategory', 'getVideoGame', 'getEditor'])]
    private Collection $videoGames;

    public function __construct()
    {
        $this->videoGames = new ArrayCollection();
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

    public function getVideoGames(): ?Collection 
    {
        return $this->videoGames; 
    }
}