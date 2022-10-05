<?php

namespace App\Repository;

use App\Entity\Classroom;
use App\Entity\Yearbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Yearbook|null find($id, $lockMode = null, $lockVersion = null)
 * @method Yearbook|null findOneBy(array $criteria, array $orderBy = null)
 * @method Yearbook[]    findAll()
 * @method Yearbook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YearbookRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Yearbook::class);
    }

    public function getYearbookPaginator(int $offset = 0): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('yearbook')
            ->select('yearbook', 'school', 'owner')
            ->join('yearbook.school', 'school')
            ->join('school.owner', 'owner')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset);

        return new Paginator($queryBuilder->getQuery());
    }

    public function findOneByClassroom(Classroom $classroom): ?Yearbook
    {
        $qb = $this->createQueryBuilder('y');

        return $qb
            ->innerJoin('y.classrooms', 'c')
            ->where('c = :classroom')
            ->setParameter('classroom', $classroom)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
