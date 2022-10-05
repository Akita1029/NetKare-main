<?php

namespace App\MessageHandler;

use App\Entity\Download;
use App\Message\DownloadPreparation;
use App\Repository\DownloadRepository;
use App\Service\DownloadMaker\AllAlbumsDownloadMaker;
use App\Service\DownloadMaker\Biometric2DownloadMaker;
use App\Service\DownloadMaker\Biometric4DownloadMaker;
use App\Service\DownloadMaker\ESchoolAlbumDownloadMaker;
use App\Service\DownloadMaker\ESchoolAlbumWithStudentNameSurnameDownloadMaker;
use App\Service\DownloadMaker\ExcelDownloadMaker;
use App\Service\DownloadMaker\ExecutiveDownloadMaker;
use App\Service\DownloadMaker\Headshot2DownloadMaker;
use App\Service\DownloadMaker\Headshot4DownloadMaker;
use App\Service\DownloadMaker\Headshot8DownloadMaker;
use App\Service\DownloadMaker\TranscriptDownloadMaker;
use App\Service\DownloadMaker\YearbookDownloadMaker;
use App\Service\FileUploader;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DownloadPreparationHandler
{
    private EntityManagerInterface $entityManager;

    private Download $download;
    private DownloadRepository $downloadRepository;

    private FileUploader $fileUploader;

    private AllAlbumsDownloadMaker $allAlbumsDownloadMaker;
    private Biometric2DownloadMaker $biometric2DownloadMaker;
    private Biometric4DownloadMaker $biometric4DownloadMaker;
    private ESchoolAlbumDownloadMaker $eSchoolAlbumDownloadMaker;
    private ESchoolAlbumWithStudentNameSurnameDownloadMaker $eSchoolAlbumWithStudentNameSurnameDownloadMaker;
    private ExcelDownloadMaker $excelDownloadMaker;
    private ExecutiveDownloadMaker $executiveDownloadMaker;
    private Headshot2DownloadMaker $headshot2DownloadMaker;
    private Headshot4DownloadMaker $headshot4DownloadMaker;
    private Headshot8DownloadMaker $headshot8DownloadMaker;
    private TranscriptDownloadMaker $transcriptDownloadMaker;
    private YearbookDownloadMaker $yearbookDownloadMaker;

    private $map = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        DownloadRepository $downloadRepository,
        FileUploader $fileUploader,
        AllAlbumsDownloadMaker $allAlbumsDownloadMaker,
        Biometric2DownloadMaker $biometric2DownloadMaker,
        Biometric4DownloadMaker $biometric4DownloadMaker,
        ESchoolAlbumDownloadMaker $eSchoolAlbumDownloadMaker,
        ESchoolAlbumWithStudentNameSurnameDownloadMaker $eSchoolAlbumWithStudentNameSurnameDownloadMaker,
        ExcelDownloadMaker $excelDownloadMaker,
        ExecutiveDownloadMaker $executiveDownloadMaker,
        Headshot2DownloadMaker $headshot2DownloadMaker,
        Headshot4DownloadMaker $headshot4DownloadMaker,
        Headshot8DownloadMaker $headshot8DownloadMaker,
        TranscriptDownloadMaker $transcriptDownloadMaker,
        YearbookDownloadMaker $yearbookDownloadMaker
    ) {
        $this->entityManager = $entityManager;

        $this->downloadRepository = $downloadRepository;

        $this->fileUploader = $fileUploader;

        $this->allAlbumsDownloadMaker = $allAlbumsDownloadMaker;
        $this->biometric2DownloadMaker = $biometric2DownloadMaker;
        $this->biometric4DownloadMaker = $biometric4DownloadMaker;
        $this->eSchoolAlbumDownloadMaker = $eSchoolAlbumDownloadMaker;
        $this->eSchoolAlbumWithStudentNameSurnameDownloadMaker = $eSchoolAlbumWithStudentNameSurnameDownloadMaker;
        $this->excelDownloadMaker = $excelDownloadMaker;
        $this->executiveDownloadMaker = $executiveDownloadMaker;
        $this->headshot2DownloadMaker = $headshot2DownloadMaker;
        $this->headshot4DownloadMaker = $headshot4DownloadMaker;
        $this->headshot8DownloadMaker = $headshot8DownloadMaker;
        $this->transcriptDownloadMaker = $transcriptDownloadMaker;
        $this->yearbookDownloadMaker = $yearbookDownloadMaker;

        $this->map = [
            Download::TYPE_ALL_ALBUMS => $this->allAlbumsDownloadMaker,
            Download::TYPE_BIOMETRIC_2 => $this->biometric2DownloadMaker,
            Download::TYPE_BIOMETRIC_4 => $this->biometric4DownloadMaker,
            Download::TYPE_E_SCHOOL_ALBUM => $this->eSchoolAlbumDownloadMaker,
            Download::TYPE_E_SCHOOL_ALBUM_WITH_STUDENT_NAME_SURNAME => $this->eSchoolAlbumWithStudentNameSurnameDownloadMaker,
            Download::TYPE_EXCEL => $this->excelDownloadMaker,
            Download::TYPE_EXECUTIVE => $this->executiveDownloadMaker,
            Download::TYPE_HEADSHOT_2 => $this->headshot2DownloadMaker,
            Download::TYPE_HEADSHOT_4 => $this->headshot4DownloadMaker,
            Download::TYPE_HEADSHOT_8 => $this->headshot8DownloadMaker,
            Download::TYPE_TRANSCRIPT => $this->transcriptDownloadMaker,
            Download::TYPE_YEARBOOK => $this->yearbookDownloadMaker,
        ];
    }

    public function __invoke(DownloadPreparation $downloadPreparation)
    {
        $downloadId = $downloadPreparation->getDownloadId();

        $this->download = $this->downloadRepository->find($downloadId);

        if (!$this->download) {
            return;
        }

        $school = $this->download->getSchool();

        if (!$school) {
            return;
        }

        if (!isset($this->map[$this->download->getType()])) {
            throw new Exception('Unknown download type.');
        }

        $path = $this->map[$this->download->getType()]->make($school);

        if (file_exists($path)) {
            $file = new File($path);
            $asset = $this->fileUploader->uploadFile($file);

            $this->download
                ->setAsset($asset)
                ->setResponseDate(new DateTimeImmutable())
                ->setStatus(Download::STATUS_DONE);
        } else {
            $this->download
                ->setResponseDate(new DateTimeImmutable())
                ->setStatus(Download::STATUS_DONE);
        }

        $this->entityManager->flush();
    }
}
