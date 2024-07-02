<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserProfile $userProfile = null;

    /**
     * @var Collection<int, MicroPost>
     */
    #[ORM\ManyToMany(targetEntity: MicroPost::class, mappedBy: 'LikedBy')]
    private Collection $liked;

    /**
     * @var Collection<int, MicroPost>
     */
    #[ORM\OneToMany(targetEntity: MicroPost::class, mappedBy: 'Author')]
    private Collection $Posts;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'Author')]
    private Collection $comments;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $banHammer = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'Followers')]
    #[ORM\JoinTable('Followers')]
    private Collection $Follows;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'Follows')]
    private Collection $Followers;

    public function __construct()
    {
        $this->liked = new ArrayCollection();
        $this->Posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->Follows = new ArrayCollection();
        $this->Followers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        if($this->isVerified()){
         $roles[] ='ROLE_VERIFIED';   
        }

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(UserProfile $userProfile): static
    {
        // set the owning side of the relation if necessary
        if ($userProfile->getUser() !== $this) {
            $userProfile->setUser($this);
        }

        $this->userProfile = $userProfile;

        return $this;
    }

    /**
     * @return Collection<int, MicroPost>
     */
    public function getLiked(): Collection
    {
        return $this->liked;
    }

    public function addLiked(MicroPost $liked): static
    {
        if (!$this->liked->contains($liked)) {
            $this->liked->add($liked);
            $liked->addLikedBy($this);
        }

        return $this;
    }

    public function removeLiked(MicroPost $liked): static
    {
        if ($this->liked->removeElement($liked)) {
            $liked->removeLikedBy($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, MicroPost>
     */
    public function getPosts(): Collection
    {
        return $this->Posts;
    }

    public function addPost(MicroPost $post): static
    {
        if (!$this->Posts->contains($post)) {
            $this->Posts->add($post);
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(MicroPost $post): static
    {
        if ($this->Posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getBanHammer(): ?\DateTimeInterface
    {
        return $this->banHammer;
    }

    public function setBanHammer(?\DateTimeInterface $banHammer): static
    {
        $this->banHammer = $banHammer;

        return $this;
    }

    public function isBanned(): bool
    {
        return $this->banHammer !== null && $this->banHammer > new \DateTime();
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollows(): Collection
    {
        return $this->Follows;
    }

    public function Follow(self $follow): static
    {
        if (!$this->Follows->contains($follow)) {
            $this->Follows->add($follow);
        }

        return $this;
    }

    public function unFollow(self $follow): static
    {
        $this->Follows->removeElement($follow);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollowers(): Collection
    {
        return $this->Followers;
    }

    public function addFollower(self $follower): static
    {
        if (!$this->Followers->contains($follower)) {
            $this->Followers->add($follower);
            $follower->Follow($this);
        }

        return $this;
    }

    public function removeFollower(self $follower): static
    {
        if ($this->Followers->removeElement($follower)) {
            $follower->unFollow($this);
        }

        return $this;
    }
}
