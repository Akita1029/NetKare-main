<?php

namespace App\Repository;

use App\Entity\StudentCustomField;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StudentCustomField|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentCustomField|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentCustomField[]    findAll()
 * @method StudentCustomField[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentCustomFieldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentCustomField::class);
    }

    // /**
    //  * @return StudentCustomField[] Returns an array of StudentCustomField objects
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
    public function findOneBySomeField($value): ?StudentCustomField
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
