<?php

namespace App\Repository;

use App\Entity\YearbookStudentVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method YearbookStudentVideo|null find($id, $lockMode = null, $lockVersion = null)
 * @method YearbookStudentVideo|null findOneBy(array $criteria, array $orderBy = null)
 * @method YearbookStudentVideo[]    findAll()
 * @method YearbookStudentVideo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YearbookStudentVideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, YearbookStudentVideo::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(YearbookStudentVideo $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(YearbookStudentVideo $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return YearbookStudentVideo[] Returns an array of YearbookStudentVideo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('y.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?YearbookStudentVideo
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
