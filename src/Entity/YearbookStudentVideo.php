<?php

namespace App\Entity;

use App\Repository\YearbookStudentVideoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: YearbookStudentVideoRepository::class)]
class YearbookStudentVideo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: Yearbook::class, inversedBy: 'studentVideos')]
    #[ORM\JoinColumn(nullable: false)]
    private $yearbook;
    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'yearbookVideos')]
    #[ORM\JoinColumn(nullable: false)]
    private $student;
    #[ORM\Column(type: 'string', length: 255)]
    private $youtubeVideoId;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getYearbook(): ?Yearbook
    {
        return $this->yearbook;
    }
    public function setYearbook(?Yearbook $yearbook): self
    {
        $this->yearbook = $yearbook;

        return $this;
    }
    public function getStudent(): ?Student
    {
        return $this->student;
    }
    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }
    public function getYoutubeVideoId(): ?string
    {
        return $this->youtubeVideoId;
    }
    public function setYoutubeVideoId(string $youtubeVideoId): self
    {
        $this->youtubeVideoId = $youtubeVideoId;

        return $this;
    }
}
