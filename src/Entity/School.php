<?php

namespace App\Entity;

use App\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\UnicodeString;

#[ORM\Entity(repositoryClass: SchoolRepository::class)]
class School implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: Dealer::class, inversedBy: 'schools')]
    private $owner;
    #[ORM\Column(type: 'string', length: 255)]
    private $name;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $logoFilename;
    #[ORM\Column(type: 'text', nullable: true)]
    private $address;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $email;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $phone;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $phoneGsm;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $fax;
    #[ORM\Column(type: 'text', nullable: true)]
    private $note;
    #[ORM\OneToMany(targetEntity: Classroom::class, mappedBy: 'school', orphanRemoval: true)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private $classrooms;
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'school', orphanRemoval: true)]
    private $orders;
    #[ORM\OneToMany(targetEntity: Album::class, mappedBy: 'school', orphanRemoval: true)]
    private $albums;
    #[ORM\OneToMany(targetEntity: Yearbook::class, mappedBy: 'school', orphanRemoval: true)]
    private $yearbooks;
    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $password;
    #[ORM\OneToMany(targetEntity: Download::class, mappedBy: 'school', orphanRemoval: true)]
    private $downloads;
    public function __construct()
    {
        $this->classrooms = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->albums = new ArrayCollection();
        $this->yearbooks = new ArrayCollection();
        $this->downloads = new ArrayCollection();
    }
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
    public function getLogoFilename(): ?string
    {
        return $this->logoFilename;
    }
    public function setLogoFilename(?string $logoFilename): self
    {
        $this->logoFilename = $logoFilename;

        return $this;
    }
    public function getOwner(): ?Dealer
    {
        return $this->owner;
    }
    public function setOwner(?Dealer $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
    public function getAddress(): ?string
    {
        return $this->address;
    }
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
    public function getPhoneGsm(): ?string
    {
        return $this->phoneGsm;
    }
    public function setPhoneGsm(?string $phoneGsm): self
    {
        $this->phoneGsm = $phoneGsm;

        return $this;
    }
    public function getFax(): ?string
    {
        return $this->fax;
    }
    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

        return $this;
    }
    public function getNote(): ?string
    {
        return $this->note;
    }
    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
    public function __toString()
    {
        return $this->name;
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
            $classroom->setSchool($this);
        }

        return $this;
    }
    public function removeClassroom(Classroom $classroom): self
    {
        if ($this->classrooms->removeElement($classroom)) {
            // set the owning side to null (unless already changed)
            if ($classroom->getSchool() === $this) {
                $classroom->setSchool(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }
    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setSchool($this);
        }

        return $this;
    }
    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getSchool() === $this) {
                $order->setSchool(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, Album>
     */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }
    public function addAlbum(Album $album): self
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
            $album->setSchool($this);
        }

        return $this;
    }
    public function removeAlbum(Album $album): self
    {
        if ($this->albums->removeElement($album)) {
            // set the owning side to null (unless already changed)
            if ($album->getSchool() === $this) {
                $album->setSchool(null);
            }
        }

        return $this;
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
            $yearbook->setSchool($this);
        }

        return $this;
    }
    public function removeYearbook(Yearbook $yearbook): self
    {
        if ($this->yearbooks->removeElement($yearbook)) {
            // set the owning side to null (unless already changed)
            if ($yearbook->getSchool() === $this) {
                $yearbook->setSchool(null);
            }
        }

        return $this;
    }
    public function getRoles(): array
    {
        return ['ROLE_SCHOOL'];
    }
    public function eraseCredentials()
    {
    }
    public function getUserIdentifier(): string
    {
        return (string) $this->id;
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
     * @return Collection<int, Download>
     */
    public function getDownloads(): Collection
    {
        return $this->downloads;
    }
    public function addDownload(Download $download): self
    {
        if (!$this->downloads->contains($download)) {
            $this->downloads->add($download);
            $download->setSchool($this);
        }

        return $this;
    }
    public function removeDownload(Download $download): self
    {
        if ($this->downloads->removeElement($download)) {
            // set the owning side to null (unless already changed)
            if ($download->getSchool() === $this) {
                $download->setSchool(null);
            }
        }

        return $this;
    }
    public function getSlug(): string
    {
        $unicodeString = new UnicodeString($this->name);

        $slugger = new AsciiSlugger();

        return $slugger->slug($unicodeString->lower());
    }
}
