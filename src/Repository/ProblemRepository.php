<?php

namespace App\Repository;

use App\Entity\Problem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Problem|null find($id, $lockMode = null, $lockVersion = null)
 * @method Problem|null findOneBy(array $criteria, array $orderBy = null)
 * @method Problem[]    findAll()
 * @method Problem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProblemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Problem::class);
    }

    /**
     * @return Contest[] Returns an array of Contest objects
     */

    public function findByTitle($title)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.title like :title')
            ->setParameter('title', '%' . $title . '%')
            ->orderBy('p.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    private function findByTagsQuery($tags)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p, t')
            ->from('App\Entity\Problem', 'p')
            ->join('p.tags', 't');
        foreach ($tags as $idx => $tag) {
            $qb->andWhere(':tag' . $idx . ' MEMBER OF p.tags')
                ->setParameter('tag' . $idx, $tag);
        }
        return $qb;
    }

    /**
     * @return Problem[] Returns an array of Problem objects
     */
    public function findByTitleAndTags($title, $tags)
    {
        $qb = $this->findByTagsQuery($tags);
        $qb->where('p.title like :title')
            ->setParameter('title', '%' . $title . '%');
        return $qb->getQuery()->getResult();

    }

    /**
     * @return Problem[] Returns an array of Problem objects
     */
    public function findByTags($tags)
    {
        return $this->findByTagsQuery($tags)->getQuery()->getResult();
    }

    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Problem
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
