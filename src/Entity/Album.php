<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'albums')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private $school;
    #[ORM\Column(type: 'boolean')]
    private $main = false;
    #[ORM\Column(type: 'string', length: 255)]
    private $name;
    #[ORM\OneToMany(targetEntity: AlbumPhoto::class, mappedBy: 'album', orphanRemoval: true)]
    private $photos;
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'album', orphanRemoval: true)]
    private $orderLines;
    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
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
    public function getMain(): ?bool
    {
        return $this->main;
    }
    public function setMain(bool $main): self
    {
        $this->main = $main;

        return $this;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
    /**
     * @return Collection<int, AlbumPhoto>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }
    public function addPhoto(AlbumPhoto $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setAlbum($this);
        }

        return $this;
    }
    public function removePhoto(AlbumPhoto $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getAlbum() === $this) {
                $photo->setAlbum(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->name;
    }
    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }
    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setAlbum($this);
        }

        return $this;
    }
    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getAlbum() === $this) {
                $orderLine->setAlbum(null);
            }
        }

        return $this;
    }
    public function isMain(): ?bool
    {
        return $this->main;
    }
}
