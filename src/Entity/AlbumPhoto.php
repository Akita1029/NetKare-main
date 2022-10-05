<?php

namespace App\Entity;

use App\Repository\AlbumPhotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumPhotoRepository::class)]
class AlbumPhoto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: Album::class, inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: false)]
    private $album;
    #[ORM\OneToOne(targetEntity: Image::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $image;
    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'photos')]
    private $student;
    #[ORM\Column(type: 'boolean')]
    private $came = true;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getAlbum(): ?Album
    {
        return $this->album;
    }
    public function setAlbum(?Album $album): self
    {
        $this->album = $album;

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
    public function getStudent(): ?Student
    {
        return $this->student;
    }
    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }
    public function getCame(): ?bool
    {
        return $this->came;
    }
    public function setCame(bool $came): self
    {
        $this->came = $came;

        return $this;
    }
    public function isCame(): ?bool
    {
        return $this->came;
    }
}
