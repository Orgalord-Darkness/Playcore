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
    }

    public function create(VideoGame $videoGame): void
    {
        $em = $this->getEntityManager();
        $em->persist($videoGame);
        $em->flush();
    }

    public function findById(int $id): ?VideoGame
    {
        return $this->find($id);
    }

    public function findAllVideoGames(): array
    {
        return $this->findAll();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function delete(VideoGame $videoGame): void
    {
        $em = $this->getEntityManager();
        $em->remove($videoGame);
        $em->flush();
    }
}
