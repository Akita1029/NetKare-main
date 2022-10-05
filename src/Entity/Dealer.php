<?php

namespace App\Entity;

use App\Repository\DealerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: DealerRepository::class)]
class Dealer implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;
    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    private $password;
    #[ORM\Column(type: 'string', length: 255)]
    private $companyName;
    #[ORM\Column(type: 'string', length: 255)]
    private $authorizedPersonName;
    #[ORM\Column(type: 'text')]
    private $address;
    #[ORM\Column(type: 'string', length: 255)]
    private $phone;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $phoneGsm;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $fax;
    #[ORM\OneToMany(targetEntity: School::class, mappedBy: 'owner')]
    private $schools;

    #[ORM\OneToMany(mappedBy: 'dealer', targetEntity: DealerPermission::class, orphanRemoval: true)]
    private Collection $permissions;
    public function __construct()
    {
        $this->schools = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return ['ROLE_DEALER'];
    }
    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }
    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }
    public function getAuthorizedPersonName(): ?string
    {
        return $this->authorizedPersonName;
    }
    public function setAuthorizedPersonName(string $authorizedPersonName): self
    {
        $this->authorizedPersonName = $authorizedPersonName;

        return $this;
    }
    public function getAddress(): ?string
    {
        return $this->address;
    }
    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    public function setPhone(string $phone): self
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
    /**
     * @return Collection<int, School>
     */
    public function getSchools(): Collection
    {
        return $this->schools;
    }
    public function addSchool(School $school): self
    {
        if (!$this->schools->contains($school)) {
            $this->schools->add($school);
            $school->setOwner($this);
        }

        return $this;
    }
    public function removeSchool(School $school): self
    {
        if ($this->schools->removeElement($school)) {
            // set the owning side to null (unless already changed)
            if ($school->getOwner() === $this) {
                $school->setOwner(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->companyName;
    }

    /**
     * @return Collection<int, DealerPermission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(DealerPermission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
            $permission->setDealer($this);
        }

        return $this;
    }

    public function removePermission(DealerPermission $permission): self
    {
        if ($this->permissions->removeElement($permission)) {
            // set the owning side to null (unless already changed)
            if ($permission->getDealer() === $this) {
                $permission->setDealer(null);
            }
        }

        return $this;
    }
}
