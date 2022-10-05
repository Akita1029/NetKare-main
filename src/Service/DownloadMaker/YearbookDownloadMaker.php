<?php

namespace App\Service\DownloadMaker;

use App\Entity\School;
use App\Repository\MemoirRepository;
use App\Repository\YearbookRepository;
use App\Repository\YearbookStudentImageRepository;
use App\Repository\YearbookStudentVideoRepository;
use App\Service\ObjectStorage;
use Twig\Environment;

class YearbookDownloadMaker extends ZipMaker implements DownloadMakerInterface
{
    private string $assetsDirectory;
    private string $schoolsDirectory;
    private string $nodeModulesDirectory;

    private MemoirRepository $memoirRepository;
    private YearbookRepository $yearbookRepository;
    private YearbookStudentImageRepository $yearbookStudentImageRepository;
    private YearbookStudentVideoRepository $yearbookStudentVideoRepository;

    private ObjectStorage $objectStorage;

    private Environment $twig;

    public function __construct(
        string $assetsDirectory,
        string $schoolsDirectory,
        string $nodeModulesDirectory,
        MemoirRepository $memoirRepository,
        YearbookRepository $yearbookRepository,
        YearbookStudentImageRepository $yearbookStudentImageRepository,
        YearbookStudentVideoRepository $yearbookStudentVideoRepository,
        ObjectStorage $objectStorage,
        Environment $twig
    ) {
        $this->assetsDirectory = $assetsDirectory;
        $this->schoolsDirectory = $schoolsDirectory;
        $this->nodeModulesDirectory = $nodeModulesDirectory;

        $this->memoirRepository = $memoirRepository;
        $this->yearbookRepository = $yearbookRepository;
        $this->yearbookStudentImageRepository = $yearbookStudentImageRepository;
        $this->yearbookStudentVideoRepository = $yearbookStudentVideoRepository;

        $this->objectStorage = $objectStorage;

        $this->twig = $twig;

        parent::__construct();
    }

    private function addAssets(School $school): void
    {
        // style
        $this->addFile(
            $this->assetsDirectory . '/styles/yearbook.css',
            $school->getSlug() . '/assets/stylesheets/yearbook.css'
        );

        // fancybox style
        $this->addFile(
            $this->nodeModulesDirectory . '/@fancyapps/ui/dist/fancybox.css',
            $school->getSlug() . '/assets/stylesheets/fancybox.css'
        );

        // fancybox script
        $this->addFile(
            $this->nodeModulesDirectory . '/@fancyapps/ui/dist/fancybox.umd.js',
            $school->getSlug() . '/assets/javascripts/fancybox.umd.js'
        );

        $this->addFile(
            $this->assetsDirectory . '/images/bg.jpg',
            $school->getSlug() . '/assets/images/bg.jpg'
        );

        $this->addFile(
            $this->assetsDirectory . '/images/bottom.svg',
            $school->getSlug() . '/assets/images/bottom.svg'
        );

        $this->addFile(
            $this->assetsDirectory . '/images/label.png',
            $school->getSlug() . '/assets/images/label.png'
        );
    }

    public function make(School $school): string
    {
        $yearbook = $this->yearbookRepository->findOneBy(['school' => $school]);

        if (!$yearbook) {
            return null;
        }

        $this->addAssets($school);

        $galleryPhotos = [];

        foreach ($yearbook->getGalleryAlbums() as $album) {
            foreach ($album->getPhotos() as $photo) {
                $key = $photo->getImage()->getAsset()->getName();

                $name = $school->getSlug() . '/assets/images/' . $key;
                $content = $this->objectStorage->getObject($key)['Body']->getContents();

                $this->addFromString($name, $content);

                $galleryPhotos[] = $key;
            }
        }

        $classrooms = $yearbook->getClassrooms();

        $content = $this->twig->render('yearbook/1.html.twig', [
            'school' => $school,
            'classrooms' => $classrooms
        ]);

        $this->addFromString(
            $school->getSlug() . '/index.html',
            $content
        );

        $this->addFile(
            $this->schoolsDirectory . '/' . $school->getLogoFilename(),
            $school->getSlug() . '/assets/images/logo.png'
        );

        foreach ($classrooms as $classroom) {
            $students = $classroom->getStudents();

            $content = $this->twig->render('yearbook/2.html.twig', [
                'school' => $school,
                'students' => $students
            ]);

            $this->addFromString(
                $school->getSlug() . '/' . $classroom->getSlug() . '.html',
                $content
            );

            foreach ($students as $student) {
                $criteria = [
                    'yearbook' => $yearbook,
                    'student' => $student
                ];

                $images = $this->yearbookStudentImageRepository->findBy($criteria);

                foreach ($images as $image) {
                    $key = $image->getImage()->getAsset()->getName();

                    $name = $school->getSlug() . '/assets/images/' . $key;
                    $content = $this->objectStorage->getObject($key)['Body']->getContents();

                    $this->addFromString($name, $content);
                }

                $videos = $this->yearbookStudentVideoRepository->findBy($criteria);

                $content = $this->twig->render('yearbook/3.html.twig', [
                    'school' => $school,
                    'student' => $student,
                    'memoir_self' => $this->memoirRepository->findOneBy(['sender' => $student, 'receiver' => $student]),
                    'memoirs' => $this->memoirRepository->getWrittenForMe($student),
                    'gallery_photos' => $galleryPhotos,
                    'images' => $images,
                    'videos' => $videos
                ]);

                $this->addFromString(
                    $school->getSlug() . '/' . $student->getSlug() . '.html',
                    $content
                );
            }
        }

        $this->close();

        return $this->getFilename();
    }
}
