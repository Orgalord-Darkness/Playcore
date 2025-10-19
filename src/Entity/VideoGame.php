<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\MaxDepth; 
use Symfony\Component\Serializer\Annotation\Groups; 
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\VideoGameRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Entity\Category; 
use App\Entity\Editor; 

#[ORM\Entity(repositoryClass: VideoGameRepository::class)]
class VideoGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getVideoGame'])]
    #[Assert\Type(type: 'integer', message: 'L\'ID doit être un entier.')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Un titre est obligatoire.")]
    #[Assert\Type(type: 'string', message: 'Le titre doit être une chaîne de caractères.')]
    #[Assert\NotBlank(message: "un titre est obligatoire")]
    #[MaxDepth(1)]
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de sortie est obligatoire.")]
    #[Assert\Type(type: \DateTimeInterface::class, message: 'La date de sortie doit être une date valide.')]
    #[Assert\NotBlank(message: "la date de sortie est obligatoire")]
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame'])]
    private ?\DateTime $releaseDate = null;

    #[ORM\Column(length: 1000)]
    #[Assert\NotBlank(message: "Une description est obligatoire.")]
    #[Assert\Type(type: 'string', message: 'La description doit être une chaîne de caractères.')]
    #[Assert\NotBlank(message: "une description est obligatoire")]
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame'])]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'videoGames')]
    #[ORM\JoinTable(name: 'video_game_category')]
    #[Assert\Type(type: Collection::class, message: 'Les catégories doivent être une collection.')]
    #[Assert\Valid]
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame', 'getCategory','getEditor'])]
    #[MaxDepth(1)]
    private ?Collection $categories = null;

    #[ORM\ManyToOne(targetEntity: Editor::class, inversedBy: 'videogames' ,cascade: ['persist'])]
    #[ORM\JoinColumn(name:"editor_id", referencedColumnName:"id")]
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame', 'getCategory','getEditor'])]
    #[MaxDepth(1)]
    #[Assert\NotNull(message: "L'éditeur est obligatoire.")]
    #[Assert\Valid]
    private ?Editor $editor = null;

    #[ORM\Column(length:255, nullable:true)]
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame'])]
    #[Assert\Type(type: 'string', message: 'L\'image de couverture doit être une chaîne de caractères.')]
    private ?string $coverImage = null;
    
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Collection
    {
        return $this->categories; 
    }

    public function setCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getEditor(): ?Editor 
    {
        return $this->editor; 
    }

    public function setEditor(Editor $editor): self 
    {
        $this->editor = $editor;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): static
    {
        $this->coverImage = $coverImage;

        return $this;
    }
}
