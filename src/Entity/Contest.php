<?php

namespace App\Entity;

use App\Repository\ContestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=ContestRepository::class)
 */
class Contest
{
    use TimestampableEntity;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="date")
     */
    private $startTimme;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="createdContests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\OneToMany(targetEntity=Problem::class, mappedBy="contestSource")
     */
    private $problems;

    /**
     * @ORM\ManyToMany(targetEntity=users::class, inversedBy="contests")
     */
    private $participants;

    public function __construct()
    {
        $this->problems = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStartTimme(): ?\DateTimeInterface
    {
        return $this->startTimme;
    }

    public function setStartTimme(\DateTimeInterface $startTimme): self
    {
        $this->startTimme = $startTimme;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCreator(): ?Users
    {
        return $this->creator;
    }

    public function setCreator(?Users $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return Collection|Problem[]
     */
    public function getProblems(): Collection
    {
        return $this->problems;
    }

    public function addProblem(Problem $problem): self
    {
        if (!$this->problems->contains($problem)) {
            $this->problems[] = $problem;
            $problem->setContestSource($this);
        }

        return $this;
    }

    public function removeProblem(Problem $problem): self
    {
        if ($this->problems->removeElement($problem)) {
            // set the owning side to null (unless already changed)
            if ($problem->getContestSource() === $this) {
                $problem->setContestSource(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|users[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(users $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(users $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }
}
