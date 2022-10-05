<?php

namespace App\Repository;

use App\Entity\Classroom;
use App\Entity\Dealer;
use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Classroom|null find($id, $lockMode = null, $lockVersion = null)
 * @method Classroom|null findOneBy(array $criteria, array $orderBy = null)
 * @method Classroom[]    findAll()
 * @method Classroom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClassroomRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classroom::class);
    }

    public function findByDealer(Dealer $dealer, School $school = null)
    {
        $qb =  $this->createQueryBuilder('classroom')
            ->join('classroom.school', 'school')
            ->where('school.owner = :owner')
            ->setParameter('owner', $dealer);

        if ($school) {
            $qb
                ->where('school = :school')
                ->setParameter('school', $school);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function getClassroomPaginator(int $offset = 0, Dealer $dealer = null, School $school = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('classroom')
            ->join('classroom.school', 'school')
            ->leftJoin('classroom.students', 'students');

        if ($dealer) {
            $queryBuilder
                ->andWhere('school.owner = :dealer')
                ->setParameter('dealer', $dealer);
        }

        if ($school) {
            $queryBuilder
                ->andWhere('classroom.school = :school')
                ->setParameter('school', $school);
        }

        $queryBuilder
            ->addOrderBy('school.name', 'ASC')
            ->addOrderBy('classroom.name', 'ASC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset);

        return new Paginator($queryBuilder->getQuery());
    }
}
