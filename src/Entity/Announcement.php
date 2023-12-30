<?php

namespace App\Entity;

use App\Entity\Keyword;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AnnouncementRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: AnnouncementRepository::class)]
class Announcement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $requirements = null;

    #[ORM\Column(length: 255)]
    private ?string $experince = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4, nullable: true)]
    private ?string $salary = null;

    #[ORM\ManyToOne(inversedBy: 'announcements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recruiter = null;

    #[ORM\ManyToMany(targetEntity: Keyword::class, mappedBy: 'announcement', cascade: ['persist'], fetch: "EAGER")]
    private Collection $keywords;

    #[ORM\ManyToOne(inversedBy: 'announcements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JobType $jobType = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $deadline = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'announcements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'appliedJobs')]
    #[ORM\JoinTable(name: "users_applications")]
    private Collection $appliedUsers;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'savedJobs')]
    private Collection $users;

    public function __construct()
    {
        $this->keywords = new ArrayCollection();
        $this->appliedUsers = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setInitialStatus(): void
    {
        $this->status = 'active';
    }

    public function getStatus(): ?string
    {
        if ($this->getDeadline() && $this->getDeadline() < new \DateTime()) {
            $this->status = 'ended';
        }

        return $this->status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRequirements(): ?string
    {
        return $this->requirements;
    }

    public function setRequirements(string $requirements): static
    {
        $this->requirements = $requirements;

        return $this;
    }

    public function getExperince(): ?string
    {
        return $this->experince;
    }

    public function setExperince(string $experince): static
    {
        $this->experince = $experince;

        return $this;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(string $salary): static
    {
        $this->salary = $salary;

        return $this;
    }

    public function getRecruiter(): ?User
    {
        return $this->recruiter;
    }

    public function setRecruiter(?User $recruiter): static
    {
        $this->recruiter = $recruiter;

        return $this;
    }

    /**
     * @return Collection<int, Keyword>
     */
    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    public function addKeyword(Keyword $keyword): static
    {
        if (!$this->keywords->contains($keyword)) {
            $this->keywords->add($keyword);
            $keyword->addAnnouncement($this);
        }

        return $this;
    }

    public function removeKeyword(Keyword $keyword): static
    {
        if ($this->keywords->removeElement($keyword)) {
            $keyword->removeAnnouncement($this);
        }

        return $this;
    }

    public function getJobType(): ?JobType
    {
        return $this->jobType;
    }

    public function setJobType(?JobType $jobType): static
    {
        $this->jobType = $jobType;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    /* public function getStatus(): ?string
    {
        return $this->status;
    } */

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
    public function generateSlug(): void
    {
        $titlePrefix = substr($this->title, 0, 4);
        $uniqueId = uniqid();

        $this->setSlug(strtolower($titlePrefix . '-' . $uniqueId));
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAppliedUsers(): Collection
    {
        return $this->appliedUsers;
    }

    public function addAppliedUser(User $appliedUser): static
    {
        if (!$this->appliedUsers->contains($appliedUser)) {
            $this->appliedUsers->add($appliedUser);
        }

        return $this;
    }

    public function removeAppliedUser(User $appliedUser): static
    {
        $this->appliedUsers->removeElement($appliedUser);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addSavedJob($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeSavedJob($this);
        }

        return $this;
    }
}
