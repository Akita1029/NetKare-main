<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\OneToOne(targetEntity: Asset::class, inversedBy: 'image', cascade: ['persist', 'remove'], fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $asset;
    #[ORM\Column(type: 'smallint')]
    private $width;
    #[ORM\Column(type: 'smallint')]
    private $height;
    /**
     * @Ignore
     */
    #[ORM\ManyToOne(targetEntity: Image::class, inversedBy: 'children', cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $parent;
    /**
     * @Ignore
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'parent', cascade: ['persist'])]
    private $children;
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getAsset(): ?Asset
    {
        return $this->asset;
    }
    public function setAsset(Asset $asset): self
    {
        $this->asset = $asset;

        return $this;
    }
    public function getWidth(): ?int
    {
        return $this->width;
    }
    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }
    public function getHeight(): ?int
    {
        return $this->height;
    }
    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }
    public function getParent(): ?self
    {
        return $this->parent;
    }
    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
    /**
     * @return Collection<int, Image>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }
    public function addChild(Image $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }
    public function removeChild(Image $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }
}