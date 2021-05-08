<?php

namespace App\Repository;

use App\Entity\SampleInput;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SampleInput|null find($id, $lockMode = null, $lockVersion = null)
 * @method SampleInput|null findOneBy(array $criteria, array $orderBy = null)
 * @method SampleInput[]    findAll()
 * @method SampleInput[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SampleInputRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SampleInput::class);
    }

    // /**
    //  * @return SampleInput[] Returns an array of SampleInput objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SampleInput
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
