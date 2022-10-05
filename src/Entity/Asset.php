<?php

namespace App\Entity;

use App\Repository\AssetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: AssetRepository::class)]
class Asset
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\Column(type: 'string', length: 255)]
    private $name;
    #[ORM\Column(type: 'bigint')]
    private $size;
    #[ORM\Column(type: 'string', length: 255)]
    private $mimeType;
    /**
     * @Ignore
     */
    #[ORM\OneToOne(targetEntity: Image::class, mappedBy: 'asset', cascade: ['persist', 'remove'])]
    private $image;
    #[ORM\Column(type: 'boolean')]
    private $public = false;
    public function getId(): ?int
    {
        return $this->id;
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
    public function getSize(): ?string
    {
        return $this->size;
    }
    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }
    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }
    public function getImage(): ?Image
    {
        return $this->image;
    }
    public function setImage(?Image $image): self
    {
        // unset the owning side of the relation if necessary
        if ($image === null && $this->image !== null) {
            $this->image->setAsset(null);
        }

        // set the owning side of the relation if necessary
        if ($image !== null && $image->getAsset() !== $this) {
            $image->setAsset($this);
        }

        $this->image = $image;

        return $this;
    }
    public function getPublic(): ?bool
    {
        return $this->public;
    }
    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }
    public function isPublic(): ?bool
    {
        return $this->public;
    }
}