<?php

namespace App\Entity;

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
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: VideoGame::class, inversedBy: 'videogames')]
    private ?Collection $videogames = null;

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

    public function getVideoGames(): Collection
    {
        return $this->videogames;
    }

    public function setVideoGame(VideoGame $videoGame): static
    {
        if(!$this->videogames->contains($videoGame)) {
            $this->videogames->add($videoGame);
        }

        return $this;
    }
}