<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    const STATUS_PENDING = 'pending';
    const STATUS_WAITING_FOR_APPROVAL = 'waiting_for_approval';
    const STATUS_WAITING_FOR_PAYMENT = 'waiting_for_payment';
    const STATUS_PROCESSED = 'processed';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: School::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private $school;
    #[ORM\Column(type: 'string', length: 255)]
    private $status = self::STATUS_PENDING;
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'parentOrder', orphanRemoval: true)]
    private $orderLines;
    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
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
    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function setStatus(string $status): self
    {
        $this->status = $status;

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
            $orderLine->setParentOrder($this);
        }

        return $this;
    }
    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getParentOrder() === $this) {
                $orderLine->setParentOrder(null);
            }
        }

        return $this;
    }
}
