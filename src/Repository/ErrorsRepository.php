<?php

namespace App\Repository;

use App\Entity\Errors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Errors|null find($id, $lockMode = null, $lockVersion = null)
 * @method Errors|null findOneBy(array $criteria, array $orderBy = null)
 * @method Errors[]    findAll()
 * @method Errors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ErrorsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Errors::class);
    }

    // /**
    //  * @return Errors[] Returns an array of Errors objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Errors
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
