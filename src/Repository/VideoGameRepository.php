<?php

namespace App\Repository;

use App\Entity\VideoGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VideoGame>
 */
class VideoGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoGame::class);
    }

    //    /**
    //     * @return VideoGame[] Returns an array of VideoGame objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?VideoGame
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllWithPagination($page, $limit): array
    {
        $qb = $this->createQueryBuilder('v')
        ->select('v', 'e', 'c')
        ->join('v.editor','e')
        ->join('v.categories', 'c')
        ->addSelect('e','c')
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit);
        return $qb->getQuery()->getResult();

        return $qb->getQuery()->getResult();  // Exécuter la requête et retourner les résultats


    }

    public function create(VideoGame $videoGame): void
    {
        $em = $this->getEntityManager();
        $em->persist($videoGame);
        $em->flush();
    }

    // READ by ID
    public function findById(int $id): ?VideoGame
    {
        return $this->find($id);
    }

    // READ all
    public function findAllVideoGames(): array
    {
        return $this->findAll();
    }

    // UPDATE (the entity should already be managed, just flush changes)
    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    // DELETE
    public function delete(VideoGame $videoGame): void
    {
        $em = $this->getEntityManager();
        $em->remove($videoGame);
        $em->flush();
    }
}
