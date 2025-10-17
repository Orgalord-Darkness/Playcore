<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    //    /**
    //     * @return Category[] Returns an array of Category objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Category
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllWithPagination($page, $limit): array
    {
        // $qb = $this->createQueryBuilder('c')
        // ->setFirstResult(($page - 1) * $limit)
        // ->setMaxResults($limit);

        $qb = $this->createQueryBuilder('c')
        ->select('c', 'vg',)  // Sélectionner Category, VideoGame et la relation de jointure (video_game_category)
        ->leftJoin('c.videoGames', 'vg')  // Jointure avec VideoGame
        ->setFirstResult(($page - 1) * $limit)  // Pagination (offset)
        ->setMaxResults($limit);  // Limiter le nombre de résultats

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
