<?php

namespace App\Entity;

use App\Repository\InputRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InputRepository::class)
 */
class Input
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $input;

    /**
     * @ORM\Column(type="text")
     */
    private $output;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInput(): ?string
    {
        return $this->input;
    }

    public function setInput(string $input): self
    {
        $this->input = $input;

        return $this;
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function setOutput(string $output): self
    {
        $this->output = $output;

        return $this;
    }
}
