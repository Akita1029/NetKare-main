<?php

namespace App\Repository;

use App\Entity\Dealer;
use App\Entity\Order;
use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getOrderPaginator(int $offset = 0, Dealer $dealer = null, School $school = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->select('o', 'school', 'owner')
            ->join('o.school', 'school')
            ->join('school.owner', 'owner')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset);

        if ($dealer) {
            $queryBuilder
                ->where('owner = :dealer')
                ->setParameter('dealer', $dealer);
        }

        if ($school) {
            $queryBuilder
                ->andWhere('school =:school')
                ->setParameter('school', $school);
        }

        return new Paginator($queryBuilder->getQuery());
    }
}
