<?php

namespace App\Repository;

use App\Entity\Album;
use App\Entity\Classroom;
use App\Entity\Dealer;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\School;
use App\Entity\Student;
use App\Entity\Yearbook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public const PAGINATOR_PER_PAGE = 20;

    private RequestStack $requestStack;

    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        parent::__construct($registry, Student::class);
        $this->requestStack = $requestStack;
    }

    public function findByDealer(Dealer $dealer, School $school = null)
    {
        $qb = $this->createQueryBuilder('student')
            ->join('student.classroom', 'classroom')
            ->join('classroom.school', 'school')
            ->join('school.owner', 'owner')
            ->where('owner = :owner')
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

    public function findNStudentByClassroom(Classroom $classroom, $n = 0): ?Student
    {
        return $this->createQueryBuilder('student')
            ->where('student.classroom = :classroom')
            ->setParameter('classroom', $classroom)
            ->setFirstResult($n)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByAlbum(Album $album, $came = null)
    {
        $queryBuilder = $this->createQueryBuilder('student')
            ->select('student')
            ->join('student.classroom', 'classroom')
            ->join('classroom.school', 'school')
            ->join('school.albums', 'albums')
            ->where('albums = :album')
            ->setParameter('album', $album)
            ->addOrderBy('classroom.name', 'ASC')
            ->addOrderBy('student.schoolNumber', 'ASC');

        if ($came != null) {
            $queryBuilder
                ->join('albums.photos', 'photos')
                ->andWhere('photos.came = :came')
                ->setParameter('came', $came);
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function getStudentPaginator(int $offset = 0, Dealer $dealer = null, School $school = null, Classroom $classroom = null, Order $order = null, bool $orderIncluded = false): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('student')
            ->select('student', 'classroom', 'school', 'album', 'photo', 'image', 'asset')
            ->join('student.classroom', 'classroom')
            ->join('classroom.school', 'school')
            ->leftJoin('school.albums', 'album', Join::WITH, 'album.main = 1')
            ->leftJoin('album.photos', 'photo', Join::WITH, 'photo.student = student')
            ->leftJoin('photo.image', 'image')
            ->leftJoin('image.asset', 'asset');

        if ($dealer) {
            $queryBuilder
                ->andWhere('school.owner = :dealer')
                ->setParameter('dealer', $dealer);
        }

        if ($school) {
            $queryBuilder
                ->andWhere('school = :school')
                ->setParameter('school', $school);
        }

        if ($classroom) {
            $queryBuilder
                ->andWhere('classroom = :classroom')
                ->setParameter('classroom', $classroom);
        }

        if ($order) {
            $queryBuilder
                ->join('classroom.orderLines', 'ol')
                ->join('ol.parentOrder', 'o')
                ->andWhere('o = :o')
                ->setParameter('o', $order);

            if ($orderIncluded) {
                $queryBuilder
                    ->join('ol.students', 'ols', Join::WITH, 'ols.included = 1');
            }
        }

        $queryBuilder
            ->addOrderBy('school.name', 'ASC')
            ->addOrderBy('classroom.name', 'ASC')
            ->addOrderBy('student.schoolNumber', 'ASC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset);

        return new Paginator($queryBuilder->getQuery());
    }

    public function loadUserByIdentifier(string $identifier): ?Student
    {
        $request = $this->requestStack->getMainRequest();

        return $this->createQueryBuilder('student')
            ->join('student.classroom', 'classroom')
            ->andWhere('classroom.school = :school')
            ->andWhere('student.schoolNumber = :schoolNumber')
            ->setParameters([
                'school' => $request->request->get('schoolId'),
                'schoolNumber' => $identifier
            ])
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function getStudentPaginatorWithMemoir(Student $sender, Yearbook $yearbook, int $offset = 0, Classroom $classroom = null): Paginator
    {

        $qb = $this->createQueryBuilder('student');

        if ($classroom) {
            $qb
                ->where('student.classroom = :classroom')
                ->setParameter('classroom', $classroom);
        } else {
            if ($yearbook->getMemoir() === Yearbook::MEMOIR_CLASSROOM) {
                $qb
                    ->where('student.classroom = :classroom')
                    ->setParameter('classroom', $sender->getClassroom());
            }

            if ($yearbook->getMemoir() === Yearbook::MEMOIR_EVERYBODY) {
                $qb->where($qb->expr()->in('student.classroom', $yearbook->getClassrooms()->map(function ($classroom) {
                    return $classroom->getId();
                })));
            }
        }

        $qb
            ->leftJoin('student.memoriesAsReceiver', 'memoir', Join::WITH, 'memoir.sender = :sender')
            ->setParameter('sender', $sender)
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset);

        return new Paginator($qb->getQuery());
    }

    public function findByYearbook(Yearbook $yearbook)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->join('s.classroom', 'c')
            ->join('c.yearbook', 'y')
            ->where('y = :y')
            ->setParameter('y', $yearbook)
            ->getQuery()
            ->getResult();
    }

    public function findByOrder(Order $order)
    {
        return $this->createQueryBuilder('s')
            ->join('s.classroom', 'c')
            ->join('c.orderLines', 'ol')
            ->join('ol.parentOrder', 'o')
            ->where('o = :o')
            ->setParameter('o', $order)
            ->getQuery()
            ->getResult();
    }

    public function findByClassroomWithMainAlbumPhoto(Classroom $classroom)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.photos', 'p')
            ->leftJoin('p.album', 'a', Join::WITH, 'a.main = 1')
            ->where('s.classroom = :classroom')
            ->setParameter('classroom', $classroom)
            ->getQuery()
            ->getResult();
    }
}
