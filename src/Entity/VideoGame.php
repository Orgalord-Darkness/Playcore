<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
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
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "un titre est obligatoire")]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "la date de sortie est obligatoire")]
    private ?\DateTime $releaseDate = null;

    #[ORM\Column(length: 1000)]
    #[Assert\NotBlank(message: "une description est obligatoire")]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'categories')]
    private ?Collection $categories = null;

    #[ORM\ManyToOne(targetEntity: Editor::class)]
    private ?Editor $editor = null;
    

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

    public function setCategories(Collection $categories): self
    {
        $this->categories = $categories;
        return $this;
    }

    public function getEditor(): ?Editor 
    {
        return $this->categories; 
    }

    public function setEditor(Editor $editor): static 
    {
        $this->editor = $editor;

        return $this;
    }
}
