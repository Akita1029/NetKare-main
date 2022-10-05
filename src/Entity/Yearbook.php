<?php

namespace App\Entity;

use App\Repository\YearbookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: YearbookRepository::class)]
class Yearbook
{
    const MEMOIR_NOBODY = 'nobody';
    const MEMOIR_CLASSROOM = 'classroom';
    const MEMOIR_EVERYBODY = 'everybody';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'yearbooks')]
    #[ORM\JoinColumn(nullable: false)]
    private $school;
    #[ORM\ManyToMany(targetEntity: Classroom::class, inversedBy: 'yearbooks')]
    private $classrooms;
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $startsAt;
    #[ORM\Column(type: 'datetime_immutable')]
    private $endsAt;
    #[ORM\Column(type: 'string', length: 255)]
    private $memoir = self::MEMOIR_CLASSROOM;
    #[ORM\Column(type: 'boolean')]
    private $imageUpload;
    #[ORM\Column(type: 'integer')]
    private $imageUploadLimit;
    #[ORM\Column(type: 'boolean')]
    private $youtube;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $qrPrefix;
    #[ORM\ManyToOne(targetEntity: Album::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $profileAlbum;
    #[ORM\ManyToMany(targetEntity: Album::class)]
    private $galleryAlbums;
    #[ORM\OneToMany(targetEntity: YearbookStudentImage::class, mappedBy: 'yearbook', orphanRemoval: true)]
    private $studentImages;
    #[ORM\OneToMany(targetEntity: YearbookStudentVideo::class, mappedBy: 'yearbook', orphanRemoval: true)]
    private $studentVideos;
    #[ORM\OneToMany(targetEntity: Operator::class, mappedBy: 'yearbook', orphanRemoval: true)]
    private $operators;
    public function __construct()
    {
        $this->classrooms = new ArrayCollection();
        $this->galleryAlbums = new ArrayCollection();
        $this->studentImages = new ArrayCollection();
        $this->studentVideos = new ArrayCollection();
        $this->operators = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
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
    /**
     * @return Collection<int, Classroom>
     */
    public function getClassrooms(): Collection
    {
        return $this->classrooms;
    }
    public function addClassroom(Classroom $classroom): self
    {
        if (!$this->classrooms->contains($classroom)) {
            $this->classrooms->add($classroom);
        }

        return $this;
    }
    public function removeClassroom(Classroom $classroom): self
    {
        $this->classrooms->removeElement($classroom);

        return $this;
    }
    public function getStartsAt(): ?\DateTimeImmutable
    {
        return $this->startsAt;
    }
    public function setStartsAt(?\DateTimeImmutable $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }
    public function getEndsAt(): ?\DateTimeImmutable
    {
        return $this->endsAt;
    }
    public function setEndsAt(\DateTimeImmutable $endsAt): self
    {
        $this->endsAt = $endsAt;

        return $this;
    }
    public function getMemoir(): ?string
    {
        return $this->memoir;
    }
    public function setMemoir(string $memoir): self
    {
        $this->memoir = $memoir;

        return $this;
    }
    public function getImageUpload(): ?bool
    {
        return $this->imageUpload;
    }
    public function setImageUpload(bool $imageUpload): self
    {
        $this->imageUpload = $imageUpload;

        return $this;
    }
    public function getImageUploadLimit(): ?int
    {
        return $this->imageUploadLimit;
    }
    public function setImageUploadLimit(int $imageUploadLimit): self
    {
        $this->imageUploadLimit = $imageUploadLimit;

        return $this;
    }
    public function getYoutube(): ?bool
    {
        return $this->youtube;
    }
    public function setYoutube(bool $youtube): self
    {
        $this->youtube = $youtube;

        return $this;
    }
    public function getQrPrefix(): ?string
    {
        return $this->qrPrefix;
    }
    public function setQrPrefix(?string $qrPrefix): self
    {
        $this->qrPrefix = $qrPrefix;

        return $this;
    }
    public function getProfileAlbum(): ?Album
    {
        return $this->profileAlbum;
    }
    public function setProfileAlbum(?Album $profileAlbum): self
    {
        $this->profileAlbum = $profileAlbum;

        return $this;
    }
    /**
     * @return Collection<int, Album>
     */
    public function getGalleryAlbums(): Collection
    {
        return $this->galleryAlbums;
    }
    public function addGalleryAlbum(Album $galleryAlbum): self
    {
        if (!$this->galleryAlbums->contains($galleryAlbum)) {
            $this->galleryAlbums->add($galleryAlbum);
        }

        return $this;
    }
    public function removeGalleryAlbum(Album $galleryAlbum): self
    {
        $this->galleryAlbums->removeElement($galleryAlbum);

        return $this;
    }
    /**
     * @return Collection<int, YearbookStudentImage>
     */
    public function getStudentImages(): Collection
    {
        return $this->studentImages;
    }
    public function addStudentImage(YearbookStudentImage $studentImage): self
    {
        if (!$this->studentImages->contains($studentImage)) {
            $this->studentImages->add($studentImage);
            $studentImage->setYearbook($this);
        }

        return $this;
    }
    public function removeStudentImage(YearbookStudentImage $studentImage): self
    {
        if ($this->studentImages->removeElement($studentImage)) {
            // set the owning side to null (unless already changed)
            if ($studentImage->getYearbook() === $this) {
                $studentImage->setYearbook(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, YearbookStudentVideo>
     */
    public function getStudentVideos(): Collection
    {
        return $this->studentVideos;
    }
    public function addStudentVideo(YearbookStudentVideo $studentVideo): self
    {
        if (!$this->studentVideos->contains($studentVideo)) {
            $this->studentVideos->add($studentVideo);
            $studentVideo->setYearbook($this);
        }

        return $this;
    }
    public function removeStudentVideo(YearbookStudentVideo $studentVideo): self
    {
        if ($this->studentVideos->removeElement($studentVideo)) {
            // set the owning side to null (unless already changed)
            if ($studentVideo->getYearbook() === $this) {
                $studentVideo->setYearbook(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, Operator>
     */
    public function getOperators(): Collection
    {
        return $this->operators;
    }
    public function addOperator(Operator $operator): self
    {
        if (!$this->operators->contains($operator)) {
            $this->operators->add($operator);
            $operator->setYearbook($this);
        }

        return $this;
    }
    public function removeOperator(Operator $operator): self
    {
        if ($this->operators->removeElement($operator)) {
            // set the owning side to null (unless already changed)
            if ($operator->getYearbook() === $this) {
                $operator->setYearbook(null);
            }
        }

        return $this;
    }
    public function isImageUpload(): ?bool
    {
        return $this->imageUpload;
    }
    public function isYoutube(): ?bool
    {
        return $this->youtube;
    }
}
