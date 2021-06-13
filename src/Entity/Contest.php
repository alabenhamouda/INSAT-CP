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
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\OneToMany(targetEntity=Problem::class, mappedBy="contest")
     */
    private $problems;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished = false;


    /**
     * @ORM\Column(type="date")
     */
    private $start_date;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="createdContests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="contests")
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


    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection|Problem[]
     */
    public function getProblems(): Collection
    {
        return $this->problems;
    }

    public function getProblem($letter)
    {
        $problems = $this->getProblems();
        foreach ($problems as $problem) {
            if ($problem->getLetter() == $letter)
                return $problem;
        }
        return null;
    }

    public function addProblem(Problem $problem): self
    {
        if (!$this->problems->contains($problem)) {
            $this->problems[] = $problem;
            $problem->setContest($this);
        }

        return $this;
    }

    public function removeProblem(Problem $problem): self
    {
        if ($this->problems->removeElement($problem)) {
            // set the owning side to null (unless already changed)
            if ($problem->getContest() === $this) {
                $problem->setContest(null);
            }
        }

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }


    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    /**
     * @return array
     */
    public function getStatus()
    {
        $contest_start =$this->getStartDate()->getTimestamp();
        $contest_end =$contest_start+$this->getDuration()*60;
        $now=time();
        $ans=array();
        $ans=array(
            'status'=>null,
            'is_published'=>null,
            'contest_start'=>null,
            'contest_end'=>null,
        );
        $ans['is_published']=$this->getIsPublished();

        if($now<$contest_start){
            $ans['status']="not_started";
        }else if($now <$contest_end){
            $ans['status']="running";
        }else{
            $ans['status']="finished";
        }
        $ans['contest_start']=$contest_start;
        $ans['contest_end']=$contest_end;
        $ans['remaining_time_before_end']=$contest_end-$now;
        $ans['remaining_time_before_start']=$contest_start-$now;
        $a=new \DateTime();$b=new \DateTime();$c=new \DateTime();
//        $a->setTimestamp(0);$b->setTimestamp(1);$c->setTimestamp(10);
//        dump($a,$b,$c);
//        dd($ans);
        return $ans;
    }



}
