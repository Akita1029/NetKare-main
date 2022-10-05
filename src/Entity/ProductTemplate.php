<?php

namespace App\Entity;

use App\Repository\ProductTemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductTemplateRepository::class)]
class ProductTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'templates')]
    #[ORM\JoinColumn(nullable: false)]
    private $product;
    #[ORM\Column(type: 'string', length: 255)]
    private $title;
    #[ORM\Column(type: 'string', length: 255)]
    private $preview1Title;
    #[ORM\Column(type: 'string', length: 255)]
    private $preview1ImageFilename;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $preview2Title;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $preview2ImageFilename;
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'productTemplate')]
    private $orderLines;
    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
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
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
    public function getPreview1Title(): ?string
    {
        return $this->preview1Title;
    }
    public function setPreview1Title(string $preview1Title): self
    {
        $this->preview1Title = $preview1Title;

        return $this;
    }
    public function getPreview1ImageFilename(): ?string
    {
        return $this->preview1ImageFilename;
    }
    public function setPreview1ImageFilename(string $preview1ImageFilename): self
    {
        $this->preview1ImageFilename = $preview1ImageFilename;

        return $this;
    }
    public function getPreview2Title(): ?string
    {
        return $this->preview2Title;
    }
    public function setPreview2Title(?string $preview2Title): self
    {
        $this->preview2Title = $preview2Title;

        return $this;
    }
    public function getPreview2ImageFilename(): ?string
    {
        return $this->preview2ImageFilename;
    }
    public function setPreview2ImageFilename(?string $preview2ImageFilename): self
    {
        $this->preview2ImageFilename = $preview2ImageFilename;

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
            $orderLine->setProductTemplate($this);
        }

        return $this;
    }
    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getProductTemplate() === $this) {
                $orderLine->setProductTemplate(null);
            }
        }

        return $this;
    }
}
