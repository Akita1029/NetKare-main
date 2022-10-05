<?php

namespace App\Repository;

use App\Entity\ProductTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductTemplate[]    findAll()
 * @method ProductTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductTemplate::class);
    }

    // /**
    //  * @return ProductTemplate[] Returns an array of ProductTemplate objects
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
    public function findOneBySomeField($value): ?ProductTemplate
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
