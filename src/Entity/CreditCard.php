<?php

namespace App\Entity;

use App\Repository\CreditCardRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreditCardRepository::class)]
class CreditCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $last4 = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $expirationMonth = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $expirationYear = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'creditCards')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $brand = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLast4(): ?int
    {
        return $this->last4;
    }

    public function setLast4(?int $last4): self
    {
        $this->last4 = $last4;
        return $this;
    }

    public function getExpirationMonth(): ?int
    {
        return $this->expirationMonth;
    }

    public function setExpirationMonth(?int $expirationMonth): self
    {
        $this->expirationMonth = $expirationMonth;
        return $this;
    }

    public function getExpirationYear(): ?int
    {
        return $this->expirationYear;
    }

    public function setExpirationYear(?int $expirationYear): self
    {
        $this->expirationYear = $expirationYear;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;
        return $this;
    }
}
