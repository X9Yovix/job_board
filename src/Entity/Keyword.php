<?php

namespace App\Entity;

use App\Repository\KeywordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KeywordRepository::class)]
class Keyword
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Announcement::class, inversedBy: 'keywords')]
    private Collection $announcement;

    public function __construct()
    {
        $this->announcement = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Announcement>
     */
    public function getAnnouncement(): Collection
    {
        return $this->announcement;
    }

    public function addAnnouncement(Announcement $announcement): static
    {
        if (!$this->announcement->contains($announcement)) {
            $this->announcement->add($announcement);
        }

        return $this;
    }

    public function removeAnnouncement(Announcement $announcement): static
    {
        $this->announcement->removeElement($announcement);

        return $this;
    }
}
