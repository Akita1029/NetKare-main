<?php

namespace App\Entity;

use App\Repository\StudentCustomFieldRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentCustomFieldRepository::class)]
class StudentCustomField
{
    const NAME_EMAIL = 'email';
    const NAME_PHONE = 'phone';
    const NAME_HOROSCOPE = 'horoscope';
    const NAME_BIRTHDATE = 'birthdate';
    const NAME_FACEBOOK = 'facebook';
    const NAME_YOUTUBE = 'youtube';
    const NAME_INSTAGRAM = 'instagram';
    const NAME_WEB = 'web';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'customFields')]
    #[ORM\JoinColumn(nullable: false)]
    private $student;
    #[ORM\Column(type: 'string', length: 255)]
    private $name;
    #[ORM\Column(type: 'string', length: 255)]
    private $value;
    public function getId(): ?int
    {
        return $this->id;
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
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
    public function getValue(): ?string
    {
        return $this->value;
    }
    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
