<?php

namespace App\Entity;

use App\Repository\ClassroomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\UnicodeString;

#[ORM\Entity(repositoryClass: ClassroomRepository::class)]
class Classroom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'classrooms')]
    #[ORM\JoinColumn(nullable: false)]
    private $school;
    #[ORM\OneToMany(targetEntity: Student::class, mappedBy: 'classroom', orphanRemoval: true)]
    #[ORM\OrderBy(['schoolNumber' => 'ASC'])]
    private $students;
    #[ORM\ManyToMany(targetEntity: OrderLine::class, mappedBy: 'classrooms')]
    private $orderLines;
    #[ORM\Column(type: 'string', length: 255)]
    private $name;
    #[ORM\ManyToMany(targetEntity: Yearbook::class, mappedBy: 'classrooms')]
    private $yearbooks;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageFilename = null;
    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
        $this->yearbooks = new ArrayCollection();
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
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }
    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->setClassroom($this);
        }

        return $this;
    }
    public function removeStudent(Student $student): self
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getClassroom() === $this) {
                $student->setClassroom(null);
            }
        }

        return $this;
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
            $orderLine->addClassroom($this);
        }

        return $this;
    }
    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            $orderLine->removeClassroom($this);
        }

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
    public function __toString()
    {
        return $this->name;
    }
    /**
     * @return Collection<int, Yearbook>
     */
    public function getYearbooks(): Collection
    {
        return $this->yearbooks;
    }
    public function addYearbook(Yearbook $yearbook): self
    {
        if (!$this->yearbooks->contains($yearbook)) {
            $this->yearbooks->add($yearbook);
            $yearbook->addClassroom($this);
        }

        return $this;
    }
    public function removeYearbook(Yearbook $yearbook): self
    {
        if ($this->yearbooks->removeElement($yearbook)) {
            $yearbook->removeClassroom($this);
        }

        return $this;
    }
    public function getSlug(): string
    {
        $unicodeString = new UnicodeString($this->name);

        $slugger = new AsciiSlugger();

        return $slugger->slug($unicodeString->lower());
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }
}
