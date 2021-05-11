<?php

namespace App\Entity;

use App\Repository\ProblemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=ProblemRepository::class)
 */
class Problem
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
    private $Letter;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $statement;

    /**
     * @ORM\Column(type="text")
     */
    private $inputSpec;

    /**
     * @ORM\Column(type="text")
     */
    private $outputSpec;

    /**
     * @ORM\Column(type="text")
     */
    private $validator;

    /**
     * @ORM\ManyToOne(targetEntity=Contest::class, inversedBy="problems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contest;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="problems")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity=SampleInput::class, mappedBy="problem")
     */
    private $sampleIn;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $solution;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $proof;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\OneToMany(targetEntity=Submission::class, mappedBy="problem")
     */
    private $submissions;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->sampleIn = new ArrayCollection();
        $this->submissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLetter(): ?string
    {
        return $this->Letter;
    }

    public function setLetter(string $Letter): self
    {
        $this->Letter = $Letter;

        return $this;
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

    public function getStatement(): ?string
    {
        return $this->statement;
    }

    public function setStatement(string $statement): self
    {
        $this->statement = $statement;

        return $this;
    }

    public function getInputSpec(): ?string
    {
        return $this->inputSpec;
    }

    public function setInputSpec(string $inputSpec): self
    {
        $this->inputSpec = $inputSpec;

        return $this;
    }

    public function getOutputSpec(): ?string
    {
        return $this->outputSpec;
    }

    public function setOutputSpec(string $outputSpec): self
    {
        $this->outputSpec = $outputSpec;

        return $this;
    }

    public function getValidator(): ?string
    {
        return $this->validator;
    }

    public function setValidator(string $validator): self
    {
        $this->validator = $validator;

        return $this;
    }

    public function getContest(): ?Contest
    {
        return $this->contest;
    }

    public function setContest(?Contest $contest): self
    {
        $this->contest = $contest;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection|SampleInput[]
     */
    public function getSampleIn(): Collection
    {
        return $this->sampleIn;
    }

    public function addSampleIn(SampleInput $sampleIn): self
    {
        if (!$this->sampleIn->contains($sampleIn)) {
            $this->sampleIn[] = $sampleIn;
            $sampleIn->setProblem($this);
        }

        return $this;
    }

    public function removeSampleIn(SampleInput $sampleIn): self
    {
        if ($this->sampleIn->removeElement($sampleIn)) {
            // set the owning side to null (unless already changed)
            if ($sampleIn->getProblem() === $this) {
                $sampleIn->setProblem(null);
            }
        }

        return $this;
    }

    public function getSolution(): ?string
    {
        return $this->solution;
    }

    public function setSolution(?string $solution): self
    {
        $this->solution = $solution;

        return $this;
    }

    public function getProof(): ?string
    {
        return $this->proof;
    }

    public function setProof(?string $proof): self
    {
        $this->proof = $proof;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    /**
     * @return Collection|Submission[]
     */
    public function getSubmissions(): Collection
    {
        return $this->submissions;
    }

    public function addSubmission(Submission $submission): self
    {
        if (!$this->submissions->contains($submission)) {
            $this->submissions[] = $submission;
            $submission->setProblem($this);
        }

        return $this;
    }

    public function removeSubmission(Submission $submission): self
    {
        if ($this->submissions->removeElement($submission)) {
            // set the owning side to null (unless already changed)
            if ($submission->getProblem() === $this) {
                $submission->setProblem(null);
            }
        }

        return $this;
    }
}
