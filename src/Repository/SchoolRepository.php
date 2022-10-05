<?php

namespace App\Repository;

use App\Entity\Dealer;
use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method School|null find($id, $lockMode = null, $lockVersion = null)
 * @method School|null findOneBy(array $criteria, array $orderBy = null)
 * @method School[]    findAll()
 * @method School[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, School::class);
    }

    public function getSchoolPaginator(int $offset = 0, Dealer $dealer = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('school');

        if ($dealer) {
            $queryBuilder
                ->andWhere('school.owner = :dealer')
                ->setParameter('dealer', $dealer);
        }

        $queryBuilder
            ->addOrderBy('school.name', 'ASC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset);

        return new Paginator($queryBuilder->getQuery());
    }
}
