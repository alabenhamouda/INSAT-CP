<?php

namespace App\Entity;

use App\Repository\SubmissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=SubmissionRepository::class)
 */
class Submission
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Problem::class, inversedBy="submissions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $problem;

    /**
     * @ORM\Column(type="text")
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="submissions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity=Status::class, inversedBy="submissions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean")
     */
    private $inContest;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProblem(): ?problem
    {
        return $this->problem;
    }

    public function setProblem(?problem $problem): self
    {
        $this->problem = $problem;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getInContest(): ?bool
    {
        return $this->inContest;
    }

    public function setInContest(bool $inContest): self
    {
        $this->inContest = $inContest;

        return $this;
    }
}
