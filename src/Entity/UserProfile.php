<?php

namespace App\Entity;

use App\Repository\UserProfileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProfileRepository::class)]
class UserProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Name = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $Bio = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $WebsiteUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $TwitterUsername = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Location = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateOfBirth = null;

    #[ORM\OneToOne(inversedBy: 'userProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(?string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->Bio;
    }

    public function setBio(?string $Bio): static
    {
        $this->Bio = $Bio;

        return $this;
    }

    public function getWebsiteUrl(): ?string
    {
        return $this->WebsiteUrl;
    }

    public function setWebsiteUrl(?string $WebsiteUrl): static
    {
        $this->WebsiteUrl = $WebsiteUrl;

        return $this;
    }

    public function getTwitterUsername(): ?string
    {
        return $this->TwitterUsername;
    }

    public function setTwitterUsername(?string $TwitterUsername): static
    {
        $this->TwitterUsername = $TwitterUsername;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->Company;
    }

    public function setCompany(?string $company): static
    {
        $this->Company = $company;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->Location;
    }

    public function setLocation(?string $Location): static
    {
        $this->Location = $Location;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->DateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $DateOfBirth): static
    {
        $this->DateOfBirth = $DateOfBirth;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
