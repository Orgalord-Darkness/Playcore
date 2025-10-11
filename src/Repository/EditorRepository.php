<?php

namespace App\Repository;

use App\Entity\Editor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EditorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Editor::class);
    }

    // CREATE ou UPDATE (persist)
    public function save(Editor $editor, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->persist($editor);

        if ($flush) {
            $em->flush();
        }
    }

    // READ - Trouver un Editor par id
    public function findOneById(int $id): ?Editor
    {
        return $this->find($id);
    }

    // READ - Trouver tous les Editors
    public function findAllWithPagination($page, $limit): array
    {
        $qb = $this->createQueryBuilder('e')
        ->setFirstResult(($page - 1) * $limit)
        ->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }

    // DELETE un Editor
    public function remove(Editor $editor, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($editor);

        if ($flush) {
            $em->flush();
        }
    }
}
