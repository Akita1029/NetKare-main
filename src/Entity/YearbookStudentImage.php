<?php

namespace App\Entity;

use App\Repository\YearbookStudentImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: YearbookStudentImageRepository::class)]
class YearbookStudentImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: Yearbook::class, inversedBy: 'studentImages')]
    #[ORM\JoinColumn(nullable: false)]
    private $yearbook;
    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'yearbookImages')]
    #[ORM\JoinColumn(nullable: false)]
    private $student;
    #[ORM\OneToOne(targetEntity: Image::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $image;
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
    public function getImage(): ?Image
    {
        return $this->image;
    }
    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }
}
