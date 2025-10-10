<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'RPG' => 'category_rpg',
            'FPS' => 'category_fps',
            'Adventure' => 'category_adventure',
            'Platformer' => 'category_platformer',
            'Puzzle' => 'category_puzzle',
            'Simulation' => 'category_simulation',
            'Horror' => 'category_horror',
            'Strategy' => 'category_strategy',
            'Racing' => 'category_racing',
            'Fighting' => 'category_fighting',
            'MMO' => 'category_mmo',
            'Stealth' => 'category_stealth',
            'Sports' => 'category_sports',
            'Sandbox' => 'category_sandbox',
            'Rhythm' => 'category_rhythm',
            'Survival' => 'category_survival',
            'Card Game' => 'category_card_game',
            'MOBA' => 'category_moba',
            'Turn-Based' => 'category_turn_based',
        ];

        foreach ($categories as $name => $referenceName) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $this->addReference($referenceName, $category);
        }

        $manager->flush();
    }
}
