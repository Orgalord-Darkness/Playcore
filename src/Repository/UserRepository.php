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

    public function save(User $user, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->persist($user);

        if ($flush) {
            $em->flush();
        }
    }

    public function findOneById(int $id): ?User
    {
        return $this->find($id);
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

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


    public function remove(User $user, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($user);

        if ($flush) {
            $em->flush();
        }
    }
}
