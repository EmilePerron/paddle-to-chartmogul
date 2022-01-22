<?php

namespace App\Repository;

use App\Entity\SyncException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SyncException|null find($id, $lockMode = null, $lockVersion = null)
 * @method SyncException|null findOneBy(array $criteria, array $orderBy = null)
 * @method SyncException[]    findAll()
 * @method SyncException[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SyncExceptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SyncException::class);
    }

    // /**
    //  * @return SyncException[] Returns an array of SyncException objects
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
    public function findOneBySomeField($value): ?SyncException
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
