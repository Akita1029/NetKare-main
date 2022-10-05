<?php

namespace App\MessageHandler;

use App\Entity\Album;
use App\Entity\AlbumPhoto;
use App\Message\EmailNotification;
use App\Message\ImportArchive;
use App\Repository\ClassroomRepository;
use App\Repository\DealerRepository;
use App\Repository\SchoolRepository;
use App\Repository\StudentRepository;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class ImportArchiveHandler
{
    private EntityManagerInterface $entityManager;
    private MessageBusInterface $messageBus;
    private DealerRepository $dealerRepository;
    private SchoolRepository $schoolRepository;
    private ClassroomRepository $classroomRepository;
    private StudentRepository $studentRepository;
    private ImageUploader $imageUploader;
    private Filesystem $filesystem;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus, DealerRepository $dealerRepository, SchoolRepository $schoolRepository, ClassroomRepository $classroomRepository, StudentRepository $studentRepository, ImageUploader $imageUploader)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
        $this->dealerRepository = $dealerRepository;
        $this->schoolRepository = $schoolRepository;
        $this->classroomRepository = $classroomRepository;
        $this->studentRepository = $studentRepository;
        $this->imageUploader = $imageUploader;

        $this->filesystem = new Filesystem;
    }

    public function __invoke(ImportArchive $importArchive)
    {
        $archivePathName = $importArchive->getArchivePathname();

        if (!file_exists($archivePathName)) {
            return;
        }

        $school = $this->schoolRepository->find($importArchive->getSchoolId());

        $formData = $importArchive->getFormData();

        $album = (new Album)
            ->setSchool($school)
            ->setName($formData['albumName']);

        $this->entityManager->persist($album);

        foreach ($formData['matches'] as $match) {
            $finder = (new Finder)
                ->in($archivePathName . '/' . $match['folder'])->files()->depth(0);

            if (!$finder->hasResults()) {
                continue;
            }

            $classroom = $this->classroomRepository->findOneBy(['school' => $school, 'name' => $match['classroom']]);

            $students = $this->studentRepository->findBy(['classroom' => $classroom], ['schoolNumber' => 'ASC']);

            $i = 0;

            foreach ($finder as $file) {
                $file = new File($file->getPathname());

                if (strpos($file->getMimeType(), 'image/') !== 0) {
                    continue;
                }

                $image = $this->imageUploader->uploadImage($file, true);
                $student = isset($students[$i]) ? $students[$i] : null;

                $albumPhoto = (new AlbumPhoto)
                    ->setAlbum($album)
                    ->setImage($image)
                    ->setStudent($student);

                $this->entityManager->persist($albumPhoto);

                $i++;
            }
        }

        $this->entityManager->flush();

        $this->filesystem->remove($archivePathName);

        $dealer = $this->dealerRepository->find($importArchive->getDealerId());
        $school = $this->schoolRepository->find($importArchive->getSchoolId());

        $email = (new Email())
            ->from('emircanok@gmail.com')
            ->to($dealer->getEmail())
            ->subject('İçe Aktarma')
            ->text($school->getName() . ' adlı okulun fotoğrafları arşiv dosyası ile içe aktarılmıştır.');

        $this->messageBus->dispatch(new EmailNotification($email));
    }
}
