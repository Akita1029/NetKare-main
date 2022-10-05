<?php

namespace App\Repository;

use App\Entity\AlbumPhoto;
use App\Entity\Classroom;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AlbumPhoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlbumPhoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlbumPhoto[]    findAll()
 * @method AlbumPhoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlbumPhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlbumPhoto::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(AlbumPhoto $entity, bool $flush = true): void
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
    public function remove(AlbumPhoto $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function filter($albumId, $data = [], $sort = false)
    {
        $queryBuilder = $this->createQueryBuilder('ap')
            ->select('ap', 's', 'image', 'asset')
            ->where('ap.album = :albumId')
            ->setParameter('albumId', $albumId)
            ->leftJoin('ap.student', 's')
            ->join('ap.image', 'image')
            ->join('image.asset', 'asset');

        if (isset($data['classroom'])) {
            $queryBuilder
                ->andWhere('s.classroom = :classroom')
                ->setParameter('classroom', $data['classroom']);
        }

        if (isset($data['schoolNumber'])) {
            $queryBuilder
                ->andWhere('s.schoolNumber = :schoolNumber')
                ->setParameter('schoolNumber', $data['schoolNumber']);
        }

        if (isset($data['name'])) {
            $queryBuilder
                ->andWhere('s.name = :name')
                ->setParameter('name', $data['name']);
        }

        if ($sort) {
            $queryBuilder
                ->orderBy('asset.size', 'ASC');
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function getStudentPhotos(Student $student)
    {
        return $this->createQueryBuilder('albumPhoto')
            ->select('albumPhoto', 'image', 'asset')
            ->where('albumPhoto.student = :student')
            ->setParameter('student', $student)
            ->join('albumPhoto.album', 'album')
            ->join('albumPhoto.image', 'image')
            ->join('image.asset', 'asset')
            ->orderBy('album.main', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getMainAlbumPhotoByClassroom(Classroom $classroom): ?array
    {
        return $this->createQueryBuilder('ap')
            ->join('ap.album', 'a')
            ->join('ap.student', 's')
            ->where('a.main = :main')
            ->setParameter('main', true)
            ->andWhere('s.classroom = :classroom')
            ->setParameter('classroom', $classroom)
            ->getQuery()
            ->getResult();
    }
}
