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
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "un titre est obligatoire")]
    #[MaxDepth(1)]
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "la date de sortie est obligatoire")]
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame'])]
    private ?\DateTime $releaseDate = null;

    #[ORM\Column(length: 1000)]
    #[Assert\NotBlank(message: "une description est obligatoire")]
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame'])]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'videoGames')]
    #[ORM\JoinTable(name: 'video_game_category')]
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame'])]
    #[MaxDepth(1)]
    private ?Collection $categories = null;

    #[ORM\ManyToOne(targetEntity: Editor::class, inversedBy: 'videogames' ,cascade: ['persist'])]
    #[ORM\JoinColumn(name:"editor_id", referencedColumnName:"id")]
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame'])]
    #[MaxDepth(1)]
    private ?Editor $editor = null;

    #[ORM\Column(length:255, nullable:true)]
    #[Groups(['getVideoGame', 'createVideoGame', 'updateVideoGame'])]
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
