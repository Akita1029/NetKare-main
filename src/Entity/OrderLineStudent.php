<?php

namespace App\Entity;

use App\Repository\OrderLineStudentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderLineStudentRepository::class)]
#[ORM\Table]
#[ORM\UniqueConstraint(name: 'match', fields: ['orderLine', 'student'])]
class OrderLineStudent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: OrderLine::class, inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    private $orderLine;
    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'orderLines')]
    #[ORM\JoinColumn(nullable: false)]
    private $student;
    #[ORM\Column(type: 'boolean')]
    private $included = false;
    #[ORM\Column(type: 'boolean')]
    private $missed = false;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getOrderLine(): ?OrderLine
    {
        return $this->orderLine;
    }
    public function setOrderLine(?OrderLine $orderLine): self
    {
        $this->orderLine = $orderLine;

        return $this;
    }
    public function getStudent(): ?Student
    {
        return $this->student;
    }
    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }
    public function getIncluded(): ?bool
    {
        return $this->included;
    }
    public function setIncluded(bool $included): self
    {
        $this->included = $included;

        return $this;
    }
    public function getMissed(): ?bool
    {
        return $this->missed;
    }
    public function setMissed(bool $missed): self
    {
        $this->missed = $missed;

        return $this;
    }
    public function isIncluded(): ?bool
    {
        return $this->included;
    }
    public function isMissed(): ?bool
    {
        return $this->missed;
    }
}
