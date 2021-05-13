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
            ->setParameter('title', '%'.$title.'%')
            ->orderBy('p.title', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    /**
     * @return Contest[] Returns an array of Contest objects
     */

    public function findByTitleAndTags($title , $tags)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from('App\Entity\Problem', 'p')
            ->where('p.title like :title')
            ->innerJoin('p.tags','tag')
            ->andWhere('tag In (:tags)')
            ->setParameter('title', '%'.$title.'%')
            ->setParameter('tags', $tags)
        ;
        return   $qb->getQuery()->getResult();

    }
    public function findByTags($tags)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('p')
            ->from('App\Entity\Problem', 'p')
            ->innerJoin('p.tags','tag')
           ->where('tag In (:tags)')
         ->setParameter('tags', $tags)
            ;
        return   $qb->getQuery()->getResult();
}

    // /**
    //  * @return Problem[] Returns an array of Problem objects
    //  */
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
