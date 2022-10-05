<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\Column(type: 'string', length: 255)]
    private $imageFilename;
    #[ORM\Column(type: 'string', length: 255)]
    private $title;
    #[ORM\Column(type: 'text', nullable: true)]
    private $description;
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private $price;
    #[ORM\OneToMany(targetEntity: ProductOption::class, mappedBy: 'product', orphanRemoval: true)]
    private $options;
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    private $category;
    #[ORM\Column(type: 'boolean')]
    private $canSelectMultipleOption = false;
    #[ORM\Column(type: 'boolean')]
    private $canSelectTemplate = false;
    #[ORM\Column(type: 'boolean')]
    private $canFillLaboratoryReference = false;
    #[ORM\OneToMany(targetEntity: ProductTemplate::class, mappedBy: 'product', orphanRemoval: true)]
    private $templates;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $knifeTemplateFilename;
    #[ORM\OneToMany(targetEntity: ProductField::class, mappedBy: 'product', orphanRemoval: true)]
    private $fields;
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'product', orphanRemoval: true)]
    private $orderLines;
    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->templates = new ArrayCollection();
        $this->fields = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }
    public function setImageFilename(string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

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
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
     * @return Collection<int, ProductOption>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }
    public function addOption(ProductOption $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->setProduct($this);
        }

        return $this;
    }
    public function removeOption(ProductOption $option): self
    {
        if ($this->options->removeElement($option)) {
            // set the owning side to null (unless already changed)
            if ($option->getProduct() === $this) {
                $option->setProduct(null);
            }
        }

        return $this;
    }
    public function getCategory(): ?Category
    {
        return $this->category;
    }
    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
    public function getCanSelectMultipleOption(): ?bool
    {
        return $this->canSelectMultipleOption;
    }
    public function setCanSelectMultipleOption(bool $canSelectMultipleOption): self
    {
        $this->canSelectMultipleOption = $canSelectMultipleOption;

        return $this;
    }
    public function getCanSelectTemplate(): ?bool
    {
        return $this->canSelectTemplate;
    }
    public function setCanSelectTemplate(bool $canSelectTemplate): self
    {
        $this->canSelectTemplate = $canSelectTemplate;

        return $this;
    }
    public function getCanFillLaboratoryReference(): ?bool
    {
        return $this->canFillLaboratoryReference;
    }
    public function setCanFillLaboratoryReference(bool $canFillLaboratoryReference): self
    {
        $this->canFillLaboratoryReference = $canFillLaboratoryReference;

        return $this;
    }
    /**
     * @return Collection<int, ProductTemplate>
     */
    public function getTemplates(): Collection
    {
        return $this->templates;
    }
    public function addTemplate(ProductTemplate $template): self
    {
        if (!$this->templates->contains($template)) {
            $this->templates->add($template);
            $template->setProduct($this);
        }

        return $this;
    }
    public function removeTemplate(ProductTemplate $template): self
    {
        if ($this->templates->removeElement($template)) {
            // set the owning side to null (unless already changed)
            if ($template->getProduct() === $this) {
                $template->setProduct(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->title;
    }
    public function getKnifeTemplateFilename(): ?string
    {
        return $this->knifeTemplateFilename;
    }
    public function setKnifeTemplateFilename(?string $knifeTemplateFilename): self
    {
        $this->knifeTemplateFilename = $knifeTemplateFilename;

        return $this;
    }
    /**
     * @return Collection<int, ProductField>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }
    public function addField(ProductField $field): self
    {
        if (!$this->fields->contains($field)) {
            $this->fields->add($field);
            $field->setProduct($this);
        }

        return $this;
    }
    public function removeField(ProductField $field): self
    {
        if ($this->fields->removeElement($field)) {
            // set the owning side to null (unless already changed)
            if ($field->getProduct() === $this) {
                $field->setProduct(null);
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
            $orderLine->setProduct($this);
        }

        return $this;
    }
    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getProduct() === $this) {
                $orderLine->setProduct(null);
            }
        }

        return $this;
    }
    public function isCanSelectMultipleOption(): ?bool
    {
        return $this->canSelectMultipleOption;
    }
    public function isCanSelectTemplate(): ?bool
    {
        return $this->canSelectTemplate;
    }
    public function isCanFillLaboratoryReference(): ?bool
    {
        return $this->canFillLaboratoryReference;
    }
}
