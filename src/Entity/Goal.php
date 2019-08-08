<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GoalRepository")
 */
class Goal
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $value;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Indicator", inversedBy="goals")
     */
    private $indicator;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Iteration", inversedBy="goals")
     */
    private $iteration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getIndicator(): ?Indicator
    {
        return $this->indicator;
    }

    public function setIndicator(?Indicator $indicator): self
    {
        $this->indicator = $indicator;

        return $this;
    }

    public function getIteration(): ?Iteration
    {
        return $this->iteration;
    }

    public function setIteration(?Iteration $iteration): self
    {
        $this->iteration = $iteration;

        return $this;
    }
}
