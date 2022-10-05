<?php

namespace App\Entity;

use App\Repository\OrderLineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderLineRepository::class)]
class OrderLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: order::class, inversedBy: 'orderLines')]
    #[ORM\JoinColumn(nullable: false)]
    private $parentOrder;
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private $price;
    #[ORM\ManyToMany(targetEntity: Classroom::class, inversedBy: 'orderLines')]
    private $classrooms;
    #[ORM\ManyToOne(targetEntity: Album::class, inversedBy: 'orderLines')]
    #[ORM\JoinColumn(nullable: false)]
    private $album;
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'orderLines')]
    #[ORM\JoinColumn(nullable: false)]
    private $product;
    #[ORM\ManyToMany(targetEntity: ProductOption::class, inversedBy: 'orderLines')]
    private $productOptions;
    #[ORM\Column(type: 'text', nullable: true)]
    private $description;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $laboratoryReferance;
    #[ORM\ManyToOne(targetEntity: ProductTemplate::class, inversedBy: 'orderLines')]
    private $productTemplate;
    #[ORM\OneToMany(targetEntity: OrderLineStudent::class, mappedBy: 'orderLine', orphanRemoval: true)]
    private $students;
    public function __construct()
    {
        $this->classrooms = new ArrayCollection();
        $this->productOptions = new ArrayCollection();
        $this->students = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getParentOrder(): ?order
    {
        return $this->parentOrder;
    }
    public function setParentOrder(?order $parentOrder): self
    {
        $this->parentOrder = $parentOrder;

        return $this;
    }
    public function getPrice(): ?string
    {
        return $this->price;
    }
    public function setPrice(string $price): self
    {
        $this->price = $price;

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
    public function getAlbum(): ?Album
    {
        return $this->album;
    }
    public function setAlbum(?Album $album): self
    {
        $this->album = $album;

        return $this;
    }
    public function getProduct(): ?Product
    {
        return $this->product;
    }
    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
    /**
     * @return Collection<int, ProductOption>
     */
    public function getProductOptions(): Collection
    {
        return $this->productOptions;
    }
    public function addProductOption(ProductOption $productOption): self
    {
        if (!$this->productOptions->contains($productOption)) {
            $this->productOptions->add($productOption);
        }

        return $this;
    }
    public function removeProductOption(ProductOption $productOption): self
    {
        $this->productOptions->removeElement($productOption);

        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function getLaboratoryReferance(): ?string
    {
        return $this->laboratoryReferance;
    }
    public function setLaboratoryReferance(?string $laboratoryReferance): self
    {
        $this->laboratoryReferance = $laboratoryReferance;

        return $this;
    }
    public function getProductTemplate(): ?ProductTemplate
    {
        return $this->productTemplate;
    }
    public function setProductTemplate(?ProductTemplate $productTemplate): self
    {
        $this->productTemplate = $productTemplate;

        return $this;
    }
    /**
     * @return Collection<int, OrderLineStudent>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }
    public function addStudent(OrderLineStudent $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->setOrderLine($this);
        }

        return $this;
    }
    public function removeStudent(OrderLineStudent $student): self
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getOrderLine() === $this) {
                $student->setOrderLine(null);
            }
        }

        return $this;
    }
}
