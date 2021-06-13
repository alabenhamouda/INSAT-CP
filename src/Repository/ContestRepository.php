<?php

namespace App\Repository;

use App\Entity\Contest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contest|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contest|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contest[]    findAll()
 * @method Contest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contest::class);
    }

     /**
      * @return Contest[] Returns an array of Contest objects
      */

    public function findByTitle($title)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.title like :title')
            ->setParameter('title', '%'.$title.'%')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
    /**
     * @return Contest[] Returns an array of Contest objects
     */

    public function findupcoming()
    {
        $date = date("Y-m-d");
        return $this->createQueryBuilder('c')
            ->andWhere('c.start_date >= :date')
            ->andWhere('c.isPublished = true')
            ->setParameter('date',$date )
            ->orderBy('c.start_date', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;
    }
    /**
     * @return Contest[] Returns an array of Contest objects
     */
    public function findrecent(): array
    {
        $date = date("Y-m-d");
        return $this->createQueryBuilder('c')
            ->andWhere('c.start_date < :date')
            ->andWhere('c.isPublished = true')
            ->setParameter('date',$date )
            ->orderBy('c.start_date', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Contest[] Returns an array of Contest objects
     */

    public function findAllOrderedbyDate(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.isPublished = true')
            ->orderBy('c.start_date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Contest
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    // /**
    //  * @return Contest[] Returns an array of Contest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
