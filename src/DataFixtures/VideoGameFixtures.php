<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\VideoGame;
use App\Entity\Editor;
use App\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;

class VideoGameFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Dark Souls
        $game1 = new VideoGame();
        $game1->setTitle('Dark Souls');
        $game1->setDescription('Jeu d\'action-RPG sombre et exigeant développé par FromSoftware.');
        $game1->setReleaseDate(new \DateTime('2011-09-22'));
        $game1->setEditor($this->getReference('editor_bandai-namco', Editor::class));
        $game1->setCategories(new ArrayCollection([
            $this->getReference('category_rpg', Category::class)
        ]));
        $manager->persist($game1);

        // Elden Ring
        $gameElden = new VideoGame();
        $gameElden->setTitle('Elden Ring');
        $gameElden->setDescription('Action-RPG en monde ouvert développé par FromSoftware en collaboration avec George R.R. Martin.');
        $gameElden->setReleaseDate(new \DateTime('2022-02-25'));
        $gameElden->setEditor($this->getReference('editor_bandai-namco', Editor::class));
        $gameElden->setCategories(new ArrayCollection([
            $this->getReference('category_rpg', Category::class)
        ]));
        $manager->persist($gameElden);

        // Bloodborne
        $game2 = new VideoGame();
        $game2->setTitle('Bloodborne');
        $game2->setDescription('Action-RPG gothique, exclusif PlayStation, signé FromSoftware.');
        $game2->setReleaseDate(new \DateTime('2015-03-24'));
        $game2->setEditor($this->getReference('editor_sony', Editor::class));
        $game2->setCategories(new ArrayCollection([
            $this->getReference('category_rpg', Category::class)
        ]));
        $manager->persist($game2);

        // Sekiro: Shadows Die Twice
        $game3 = new VideoGame();
        $game3->setTitle('Sekiro: Shadows Die Twice');
        $game3->setDescription('Jeu d\'action-aventure de FromSoftware avec un gameplay exigeant.');
        $game3->setReleaseDate(new \DateTime('2019-03-22'));
        $game3->setEditor($this->getReference('editor_activision', Editor::class));
        $game3->setCategories(new ArrayCollection([
            $this->getReference('category_action', Category::class)
        ]));
        $manager->persist($game3);

        // Demon's Souls (ajouté comme 5ème jeu FromSoftware)
        $game4 = new VideoGame();
        $game4->setTitle('Demon\'s Souls');
        $game4->setDescription('Jeu d\'action-RPG sombre et difficile, précurseur de Dark Souls.');
        $game4->setReleaseDate(new \DateTime('2009-02-05'));
        $game4->setEditor($this->getReference('editor_sony', Editor::class));
        $game4->setCategories(new ArrayCollection([
            $this->getReference('category_rpg', Category::class)
        ]));
        $manager->persist($game4);

        $manager->flush();
    }
}
