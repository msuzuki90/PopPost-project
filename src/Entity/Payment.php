<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $sessionID;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $paymentStatus;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $creationDate;

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $invoice = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $customerStripeId = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $subscriptionId = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $paymentMethodId = null;

    #[ORM\Column(type: 'boolean')]
    private bool $successPageExpired;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTimeInterface $nextPayment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionID(): ?string
    {
        return $this->sessionID;
    }

    public function setSessionID(string $sessionID): self
    {
        $this->sessionID = $sessionID;
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

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(string $paymentStatus): self
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    public function getCreationDate(): ?DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getInvoice(): ?string
    {
        return $this->invoice;
    }

    public function setInvoice(?string $invoice): self
    {
        $this->invoice = $invoice;
        return $this;
    }

    public function getCustomerStripeId(): ?string
    {
        return $this->customerStripeId;
    }

    public function setCustomerStripeId(?string $customerStripeId): self
    {
        $this->customerStripeId = $customerStripeId;
        return $this;
    }

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(?string $subscriptionId): self
    {
        $this->subscriptionId = $subscriptionId;
        return $this;
    }

    public function getPaymentMethodId(): ?string
    {
        return $this->paymentMethodId;
    }

    public function setPaymentMethodId(?string $paymentMethodId): self
    {
        $this->paymentMethodId = $paymentMethodId;
        return $this;
    }

    public function getSuccessPageExpired(): ?bool
    {
        return $this->successPageExpired;
    }

    public function setSuccessPageExpired(bool $successPageExpired): self
    {
        $this->successPageExpired = $successPageExpired;
        return $this;
    }

    public function getNextPayment(): ?DateTimeInterface
    {
        return $this->nextPayment;
    }

    public function setNextPayment(?DateTimeInterface $nextPayment): self
    {
        $this->nextPayment = $nextPayment;
        return $this;
    }
}
