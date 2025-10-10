<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\VideoGame; 

class VideoGameFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Dark Souls (déjà présent)
        $game1 = new VideoGame();
        $game1->setTitle('Dark Souls');
        $game1->setDescription('Jeu d\'action-RPG sombre et exigeant développé par FromSoftware.');
        $game1->setReleaseDate(new \DateTime('2011-09-22'));
        $game1->setEditor($this->getReference('editor_bandai-namco', ''));
        $game1->setCategory($this->getReference('category_rpg', '')); 
        $manager->persist($game1);

        // Elden Ring - nouveau jeu FromSoftware
        $gameElden = new VideoGame();
        $gameElden->setTitle('Elden Ring');
        $gameElden->setDescription('Action-RPG en monde ouvert développé par FromSoftware en collaboration avec George R.R. Martin.');
        $gameElden->setReleaseDate(new \DateTime('2022-02-25'));
        $gameElden->setEditor($this->getReference('editor_bandai-namco', ''));
        $gameElden->setCategory($this->getReference('category_rpg', ''));
        $manager->persist($gameElden);

        // Bloodborne
        $game2 = new VideoGame();
        $game2->setTitle('Bloodborne');
        $game2->setDescription('Action-RPG gothique, exclusif PlayStation, signé FromSoftware.');
        $game2->setReleaseDate(new \DateTime('2015-03-24'));
        $game2->setEditor($this->getReference('editor_sony', ''));
        $game2->setCategory($this->getReference('category_rpg', ''));
        $manager->persist($game2);

        // Sekiro: Shadows Die Twice
        $game3 = new VideoGame();
        $game3->setTitle('Sekiro: Shadows Die Twice');
        $game3->setDescription('Jeu d\'action-aventure de FromSoftware avec un gameplay exigeant.');
        $game3->setReleaseDate(new \DateTime('2019-03-22'));
        $game3->setEditor($this->getReference('editor_activision', ''));
        $game3->setCategory($this->getReference('category_action', ''));
        $manager->persist($game3);

        // The Witcher 3: Wild Hunt
        $game4 = new VideoGame();
        $game4->setTitle('The Witcher 3: Wild Hunt');
        $game4->setDescription('RPG en monde ouvert, acclamé par la critique, développé par CD Projekt Red.');
        $game4->setReleaseDate(new \DateTime('2015-05-19'));
        $game4->setEditor($this->getReference('editor_cdp', ''));
        $game4->setCategory($this->getReference('category_rpg', ''));
        $manager->persist($game4);

        // Call of Duty: Modern Warfare
        $game5 = new VideoGame();
        $game5->setTitle('Call of Duty: Modern Warfare');
        $game5->setDescription('Jeu de tir à la première personne très populaire.');
        $game5->setReleaseDate(new \DateTime('2019-10-25'));
        $game5->setEditor($this->getReference('editor_activision', ''));
        $game5->setCategory($this->getReference('category_fps', ''));
        $manager->persist($game5);

        // Minecraft
        $game6 = new VideoGame();
        $game6->setTitle('Minecraft');
        $game6->setDescription('Jeu bac à sable où le joueur construit et explore.');
        $game6->setReleaseDate(new \DateTime('2011-11-18'));
        $game6->setEditor($this->getReference('editor_microsoft', ''));
        $game6->setCategory($this->getReference('category_sandbox', ''));
        $manager->persist($game6);

        // The Legend of Zelda: Breath of the Wild
        $game7 = new VideoGame();
        $game7->setTitle('The Legend of Zelda: Breath of the Wild');
        $game7->setDescription('Action-aventure en monde ouvert, acclamé par la critique.');
        $game7->setReleaseDate(new \DateTime('2017-03-03'));
        $game7->setEditor($this->getReference('editor_nintendo', ''));
        $game7->setCategory($this->getReference('category_aventure', ''));
        $manager->persist($game7);

        // FIFA 21
        $game9 = new VideoGame();
        $game9->setTitle('FIFA 21');
        $game9->setDescription('Simulation de football très réaliste et populaire.');
        $game9->setReleaseDate(new \DateTime('2020-10-09'));
        $game9->setEditor($this->getReference('editor_ea', ''));
        $game9->setCategory($this->getReference('category_sports', ''));
        $manager->persist($game9);

        // Cyberpunk 2077
        $game10 = new VideoGame();
        $game10->setTitle('Cyberpunk 2077');
        $game10->setDescription('RPG futuriste développé par CD Projekt Red.');
        $game10->setReleaseDate(new \DateTime('2020-12-10'));
        $game10->setEditor($this->getReference('editor_cdp', ''));
        $game10->setCategory($this->getReference('category_rpg', ''));
        $manager->persist($game10);

        $manager->flush();
    }
}
