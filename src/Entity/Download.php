<?php

namespace App\Entity;

use App\Repository\DownloadRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DownloadRepository::class)]
class Download
{
    const STATUS_PENDING = 'pending';
    const STATUS_DONE = 'done';
    const TYPE_E_SCHOOL_ALBUM = 'e_shool_album';
    const TYPE_E_SCHOOL_ALBUM_WITH_STUDENT_NAME_SURNAME = 'e_school_album_with_student_name_surname';
    const TYPE_ALL_ALBUMS = 'all_albums';
    const TYPE_BIOMETRIC_2 = 'biometric_2';
    const TYPE_BIOMETRIC_4 = 'biometric_4';
    const TYPE_HEADSHOT_2 = 'headshot_2';
    const TYPE_HEADSHOT_4 = 'headshot_4';
    const TYPE_HEADSHOT_8 = 'headshot_8';
    const TYPE_EXECUTIVE = 'executive';
    const TYPE_EXCEL = 'excel';
    const TYPE_TRANSCRIPT = 'transcript';
    const TYPE_YEARBOOK = 'yearbook';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\Column(type: 'string', length: 255)]
    private $status = self::STATUS_PENDING;
    #[ORM\OneToOne(targetEntity: Asset::class, cascade: ['persist', 'remove'])]
    private $asset;
    #[ORM\Column(type: 'datetime_immutable')]
    private $requestDate;
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $responseDate = null;
    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'downloads')]
    #[ORM\JoinColumn(nullable: false)]
    private $school;
    #[ORM\Column(type: 'string', length: 255)]
    private $type;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
    public function getAsset(): ?Asset
    {
        return $this->asset;
    }
    public function setAsset(?Asset $asset): self
    {
        $this->asset = $asset;

        return $this;
    }
    public function getRequestDate(): ?\DateTimeImmutable
    {
        return $this->requestDate;
    }
    public function setRequestDate(\DateTimeImmutable $requestDate): self
    {
        $this->requestDate = $requestDate;

        return $this;
    }
    public function getResponseDate(): ?\DateTimeImmutable
    {
        return $this->responseDate;
    }
    public function setResponseDate(?\DateTimeImmutable $responseDate): self
    {
        $this->responseDate = $responseDate;

        return $this;
    }
    public function getSchool(): ?School
    {
        return $this->school;
    }
    public function setSchool(?School $school): self
    {
        $this->school = $school;

        return $this;
    }
    public function getType(): ?string
    {
        return $this->type;
    }
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
