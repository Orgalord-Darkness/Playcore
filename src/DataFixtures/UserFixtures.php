<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un utilisateur standard : John Doe
        $user = new User();
        $user->setUsername('john_doe')
            ->setEmail('john.doe@example.com')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->passwordHasher->hashPassword($user, 'password123'));

        $manager->persist($user);

        // Créer un administrateur : Admin
        $admin = new User();
        $admin->setUsername('admin')
            ->setEmail('admin@example.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));

        $manager->persist($admin);

        // Créer un utilisateur Edward
        $edward = new User();
        $edward->setUsername('edward')
            ->setEmail('edward@example.com')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->passwordHasher->hashPassword($edward, 'edwardpass'));

        $manager->persist($edward);

        // Créer une utilisatrice Alice
        $alice = new User();
        $alice->setUsername('alice')
            ->setEmail('alice@example.com')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->passwordHasher->hashPassword($alice, 'alicepass'));

        $manager->persist($alice);

        // Sauvegarder les entités
        $manager->flush();
    }
}
