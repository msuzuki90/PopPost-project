<?php

namespace App\Entity;

use App\Repository\MicroPostRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MicroPostRepository::class)]
class MicroPost
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min:5,max:255,minMessage:'Title is way to short! 5 character minimum pretty please.')]
    private ?string $Title = null;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank()]
    #[Assert\Length(min:5,max:500,minMessage:'Text is way to short! 5 character minimum pretty please.')]
    private ?string $Text = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $Created = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'liked')]
    private Collection $LikedBy;

    #[ORM\ManyToOne(inversedBy: 'Posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $Author = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->LikedBy = new ArrayCollection();
        $this->Created = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): static
    {
        $this->Title = $Title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->Text;
    }

    public function setText(string $Text): static
    {
        $this->Text = $Text;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->Created;
    }

    public function setCreated(\DateTimeInterface $Created): static
    {
        $this->Created = $Created;

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
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikedBy(): Collection
    {
        return $this->LikedBy;
    }

    public function addLikedBy(User $likedBy): static
    {
        if (!$this->LikedBy->contains($likedBy)) {
            $this->LikedBy->add($likedBy);
        }

        return $this;
    }

    public function removeLikedBy(User $likedBy): static
    {
        $this->LikedBy->removeElement($likedBy);

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->Author;
    }

    public function setAuthor(?User $Author): static
    {
        $this->Author = $Author;

        return $this;
    }
}
