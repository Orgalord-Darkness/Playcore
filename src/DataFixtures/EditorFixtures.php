<?php

namespace App\DataFixtures;

use App\Entity\Editor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EditorFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $nintendo = new Editor();
        $nintendo->setName('Nintendo')
                 ->setCountry('Japan');
        $manager->persist($nintendo);
        $this->addReference('editor_nintendo', $nintendo);

        $ea = new Editor();
        $ea->setName('Electronic Arts')
           ->setCountry('USA');
        $manager->persist($ea);
        $this->addReference('editor_ea', $ea);

        $ubisoft = new Editor();
        $ubisoft->setName('Ubisoft')
                ->setCountry('France');
        $manager->persist($ubisoft);
        $this->addReference('editor_ubisoft', $ubisoft);

        $sony = new Editor();
        $sony->setName('Sony Interactive Entertainment')
             ->setCountry('Japan');
        $manager->persist($sony);
        $this->addReference('editor_sony', $sony);

        $microsoft = new Editor();
        $microsoft->setName('Microsoft Studios')
                  ->setCountry('USA');
        $manager->persist($microsoft);
        $this->addReference('editor_microsoft', $microsoft);

        $square = new Editor();
        $square->setName('Square Enix')
               ->setCountry('Japan');
        $manager->persist($square);
        $this->addReference('editor_square', $square);

        $capcom = new Editor();
        $capcom->setName('Capcom')
               ->setCountry('Japan');
        $manager->persist($capcom);
        $this->addReference('editor_capcom', $capcom);

        $activision = new Editor();
        $activision->setName('Activision')
                   ->setCountry('USA');
        $manager->persist($activision);
        $this->addReference('editor_activision', $activision);

        $manager->flush();
    }
}
