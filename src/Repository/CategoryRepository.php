<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }
    
    public function findAllWithPagination($page, $limit): array
    {
        

        $qb = $this->createQueryBuilder('c')
        ->select('c', 'vg',) 
        ->leftJoin('c.videoGames', 'vg')
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function create(Category $category): void
    {
        $this->_em->persist($category);
        $this->_em->flush();
    }

    public function findAllCategories(): array
    {
        $qb = $this->createQueryBuilder('c');
        
        return $qb->getQuery()->getResult();
    }

    public function findCategoryById(int $id): ?Category
    {
        $qb = $this->createQueryBuilder('c');
        
        return $qb
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function updateName(int $id, string $newName): void
    {
        $qb = $this->createQueryBuilder('c');
        
        $qb->update()
            ->set('c.name', ':newName')
            ->where('c.id = :id')
            ->setParameter('newName', $newName)
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    public function delete(int $id): void
    {
        $qb = $this->createQueryBuilder('c');
        
        $qb->delete()
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }
}
