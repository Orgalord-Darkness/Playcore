<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    // CREATE ou UPDATE (persist)
    public function save(User $user, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->persist($user);

        if ($flush) {
            $em->flush();
        }
    }

    // READ - Trouver un User par id
    public function findOneById(int $id): ?User
    {
        return $this->find($id);
    }

    // READ - Trouver un User par email
    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    // READ - Trouver tous les Users
    public function findAllUsers(): array
    {
        return $this->findAll();
    }

    public function findAllWithPagination($page, $limit){
        $qb = $this->createQueryBuilder('u')
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit);

        return $qb->GetQuery()->getResult();
    }

    public function findUsersBySubcription(): array 
    {
        return $this->createQueryBuilder('u')
            ->where('u.subcription_to_newsletter = :subscribed')
            ->setParameter('subscribed', true)
            ->getQuery()
            ->getResult();
    }


    // UPDATE est souvent un save sur une entité déjà chargée
    // Pas besoin d’une méthode spécifique, on utilise save()

    // DELETE un User
    public function remove(User $user, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($user);

        if ($flush) {
            $em->flush();
        }
    }
}
