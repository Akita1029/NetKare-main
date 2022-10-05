<?php

namespace App\Entity;

use App\Repository\ProductFieldRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductFieldRepository::class)]
class ProductField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\Column(type: 'string', length: 255)]
    private $description;
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'fields')]
    #[ORM\JoinColumn(nullable: false)]
    private $product;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(string $description): self
    {
        $this->description = $description;

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
}
