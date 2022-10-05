<?php

namespace App\Repository;

use App\Entity\Memoir;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Memoir|null find($id, $lockMode = null, $lockVersion = null)
 * @method Memoir|null findOneBy(array $criteria, array $orderBy = null)
 * @method Memoir[]    findAll()
 * @method Memoir[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemoirRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Memoir::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Memoir $entity, bool $flush = true): void
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
    public function remove(Memoir $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getWrittenForMe(Student $me)
    {
        $queryBuilder = $this->createQueryBuilder('m');

        $expr = $queryBuilder->expr();

        return $queryBuilder
            ->where($expr->andX(
                $expr->neq('m.sender', '?1'),
                $expr->eq('m.receiver', '?1')
            ))
            ->setParameter(1, $me)
            ->getQuery()
            ->getResult();
    }

    public function getMemoirPaginator($offset = 0, Student $sender = null, Student $receiver = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('m')
            ->select('m', 's', 'r')
            ->join('m.sender', 's')
            ->join('m.receiver', 'r')
            ->addOrderBy('s.schoolNumber')
            ->addOrderBy('r.schoolNumber')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset);

        if ($sender) {
            $queryBuilder
                ->andWhere('s = :s')
                ->setParameter('s', $sender);
        }

        if ($sender) {
            $queryBuilder
                ->andWhere('r = :r')
                ->setParameter('r', $receiver);
        }

        return new Paginator($queryBuilder->getQuery());
    }

    // /**
    //  * @return Memoir[] Returns an array of Memoir objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Memoir
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
