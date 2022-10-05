<?php

namespace App\Repository;

use App\Entity\Album;
use App\Entity\Dealer;
use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Album $entity, bool $flush = true): void
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
    public function remove(Album $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findByDealer(Dealer $dealer, School $school = null)
    {
        $qb =  $this->createQueryBuilder('album')
            ->join('album.school', 'school')
            ->where('school.owner = :owner')
            ->setParameter('owner', $dealer);

        if ($school) {
            $qb
                ->andWhere('school = :school')
                ->setParameter('school', $school);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function getAlbumPaginator(int $offset = 0, Dealer $dealer = null, School $school = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('album');

        if ($dealer) {
            $queryBuilder
                ->join('album.school', 'school')
                ->andWhere('school.owner = :dealer')
                ->setParameter('dealer', $dealer);
        }

        if ($school) {
            $queryBuilder
                ->andWhere('album.school = :school')
                ->setParameter('school', $school);
        }

        $queryBuilder
            ->addOrderBy('album.main', 'DESC')
            ->addOrderBy('album.name')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset);

        return new Paginator($queryBuilder->getQuery());
    }

    public function getMainAlbumBySchool(School $school): Album
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.main = :main')
            ->andWhere('a.school = :school')
            ->setParameters(['main' => true, 'school' => $school])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
