<?php

namespace App\Entity;

use App\Repository\MemoirRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemoirRepository::class)]
class Memoir
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'memoriesAsSender')]
    #[ORM\JoinColumn(nullable: false)]
    private $sender;
    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'memoriesAsReceiver')]
    #[ORM\JoinColumn(nullable: false)]
    private $receiver;
    #[ORM\Column(type: 'text')]
    private $text;
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getSender(): ?Student
    {
        return $this->sender;
    }
    public function setSender(?Student $sender): self
    {
        $this->sender = $sender;

        return $this;
    }
    public function getReceiver(): ?Student
    {
        return $this->receiver;
    }
    public function setReceiver(?Student $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }
    public function getText(): ?string
    {
        return $this->text;
    }
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
