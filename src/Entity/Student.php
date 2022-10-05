<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\UnicodeString;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: Classroom::class, inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    private $classroom;
    #[ORM\Column(type: 'integer')]
    private $schoolNumber;
    #[ORM\Column(type: 'string', length: 255)]
    private $name;
    #[ORM\Column(type: 'string', length: 255)]
    private $surname;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $gender;
    #[ORM\OneToMany(targetEntity: StudentCustomField::class, mappedBy: 'student', orphanRemoval: true)]
    private $customFields;
    #[ORM\OneToMany(targetEntity: AlbumPhoto::class, mappedBy: 'student', orphanRemoval: true)]
    private $photos;
    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $password;
    #[ORM\OneToMany(targetEntity: Memoir::class, mappedBy: 'sender', orphanRemoval: true)]
    private $memoriesAsSender;
    #[ORM\OneToMany(targetEntity: Memoir::class, mappedBy: 'receiver', orphanRemoval: true)]
    private $memoriesAsReceiver;
    #[ORM\OneToMany(targetEntity: YearbookStudentImage::class, mappedBy: 'student', orphanRemoval: true)]
    private $yearbookImages;
    #[ORM\OneToMany(targetEntity: YearbookStudentVideo::class, mappedBy: 'student', orphanRemoval: true)]
    private $yearbookVideos;
    #[ORM\OneToMany(targetEntity: OrderLineStudent::class, mappedBy: 'student', orphanRemoval: true)]
    private $orderLines;
    public function __construct()
    {
        $this->customFields = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->yearbookImages = new ArrayCollection();
        $this->yearbookVideos = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
        $this->memoriesAsSender = new ArrayCollection();
        $this->memoriesAsReceiver = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getClassroom(): ?Classroom
    {
        return $this->classroom;
    }
    public function setClassroom(?Classroom $classroom): self
    {
        $this->classroom = $classroom;

        return $this;
    }
    public function getSchoolNumber(): ?int
    {
        return $this->schoolNumber;
    }
    public function setSchoolNumber(int $schoolNumber): self
    {
        $this->schoolNumber = $schoolNumber;

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
    public function getSurname(): ?string
    {
        return $this->surname;
    }
    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }
    public function getGender(): ?string
    {
        return $this->gender;
    }
    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }
    /**
     * @return Collection<int, StudentCustomField>
     */
    public function getCustomFields(): Collection
    {
        return $this->customFields;
    }
    public function addCustomField(StudentCustomField $customField): self
    {
        if (!$this->customFields->contains($customField)) {
            $this->customFields->add($customField);
            $customField->setStudent($this);
        }

        return $this;
    }
    public function removeCustomField(StudentCustomField $customField): self
    {
        if ($this->customFields->removeElement($customField)) {
            // set the owning side to null (unless already changed)
            if ($customField->getStudent() === $this) {
                $customField->setStudent(null);
            }
        }

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
            $photo->setStudent($this);
        }

        return $this;
    }
    public function removePhoto(AlbumPhoto $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getStudent() === $this) {
                $photo->setStudent(null);
            }
        }

        return $this;
    }
    public function getMainPhoto(): ?AlbumPhoto
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('album.main', true));

        return $this->photos->matching($criteria)->first();
    }
    public function getRoles(): array
    {
        return ['ROLE_STUDENT'];
    }
    public function eraseCredentials()
    {
    }
    public function getUserIdentifier(): string
    {
        return (string) $this->schoolNumber;
    }
    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /**
     * @return Collection<int, Memoir>
     */
    public function getMemoriesAsSender(): Collection
    {
        return $this->memoriesAsSender;
    }
    public function addMemoriesAsSender(Memoir $memoriesAsSender): self
    {
        if (!$this->memoriesAsSender->contains($memoriesAsSender)) {
            $this->memoriesAsSender->add($memoriesAsSender);
            $memoriesAsSender->setSender($this);
        }

        return $this;
    }
    public function removeMemoriesAsSender(Memoir $memoriesAsSender): self
    {
        if ($this->memoriesAsSender->removeElement($memoriesAsSender)) {
            // set the owning side to null (unless already changed)
            if ($memoriesAsSender->getSender() === $this) {
                $memoriesAsSender->setSender(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, Memoir>
     */
    public function getMemoriesAsReceiver(): Collection
    {
        return $this->memoriesAsReceiver;
    }
    public function addMemoriesAsReceiver(Memoir $memoriesAsReceiver): self
    {
        if (!$this->memoriesAsReceiver->contains($memoriesAsReceiver)) {
            $this->memoriesAsReceiver->add($memoriesAsReceiver);
            $memoriesAsReceiver->setReceiver($this);
        }

        return $this;
    }
    public function removeMemoriesAsReceiver(Memoir $memoriesAsReceiver): self
    {
        if ($this->memoriesAsReceiver->removeElement($memoriesAsReceiver)) {
            // set the owning side to null (unless already changed)
            if ($memoriesAsReceiver->getReceiver() === $this) {
                $memoriesAsReceiver->setReceiver(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, YearbookStudentImage>
     */
    public function getYearbookImages(): Collection
    {
        return $this->yearbookImages;
    }
    public function addYearbookImage(YearbookStudentImage $yearbookImage): self
    {
        if (!$this->yearbookImages->contains($yearbookImage)) {
            $this->yearbookImages->add($yearbookImage);
            $yearbookImage->setStudent($this);
        }

        return $this;
    }
    public function removeYearbookImage(YearbookStudentImage $yearbookImage): self
    {
        if ($this->yearbookImages->removeElement($yearbookImage)) {
            // set the owning side to null (unless already changed)
            if ($yearbookImage->getStudent() === $this) {
                $yearbookImage->setStudent(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, YearbookStudentVideo>
     */
    public function getYearbookVideos(): Collection
    {
        return $this->yearbookVideos;
    }
    public function addYearbookVideo(YearbookStudentVideo $yearbookVideo): self
    {
        if (!$this->yearbookVideos->contains($yearbookVideo)) {
            $this->yearbookVideos->add($yearbookVideo);
            $yearbookVideo->setStudent($this);
        }

        return $this;
    }
    public function removeYearbookVideo(YearbookStudentVideo $yearbookVideo): self
    {
        if ($this->yearbookVideos->removeElement($yearbookVideo)) {
            // set the owning side to null (unless already changed)
            if ($yearbookVideo->getStudent() === $this) {
                $yearbookVideo->setStudent(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, OrderLineStudent>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }
    public function addOrderLine(OrderLineStudent $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setStudent($this);
        }

        return $this;
    }
    public function removeOrderLine(OrderLineStudent $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getStudent() === $this) {
                $orderLine->setStudent(null);
            }
        }

        return $this;
    }
    public function getSlug(): string
    {
        $unicodeString = new UnicodeString($this->schoolNumber . ' ' . $this->name . ' ' . $this->surname);

        $slugger = new AsciiSlugger();

        return $slugger->slug($unicodeString->lower());
    }
}
