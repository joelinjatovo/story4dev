<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $budget;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $start_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $end_at;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Periodicity", inversedBy="projects")
     */
    private $periodicity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Activity", mappedBy="project", orphanRemoval=true)
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Iteration", mappedBy="project", orphanRemoval=true)
     */
    private $iterations;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->iterations = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->start_at;
    }

    public function setStartAt(?\DateTimeInterface $start_at): self
    {
        $this->start_at = $start_at;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->end_at;
    }

    public function setEndAt(?\DateTimeInterface $end_at): self
    {
        $this->end_at = $end_at;

        return $this;
    }

    public function getPeriodicity(): ?Periodicity
    {
        return $this->periodicity;
    }

    public function setPeriodicity(?Periodicity $periodicity): self
    {
        $this->periodicity = $periodicity;

        return $this;
    }
    
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(?Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setProject($this);
        }

        return $this;
    }

    public function removeActivity(?Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            if ($activity->getProject() === $this) {
                $activity->setProject(null);
            }
        }

        return $this;
    }
    
    public function getIterations(): Collection
    {
        return $this->iterations;
    }

    public function addIteration(?Iteration $iteration): self
    {
        if (!$this->iterations->contains($iteration)) {
            $this->iterations[] = $iteration;
            $iteration->setProject($this);
        }

        return $this;
    }

    public function removeIteration(?Iteration $iteration): self
    {
        if ($this->iterations->contains($iteration)) {
            $this->iterations->removeElement($iteration);
            if ($iteration->getProject() === $this) {
                $iteration->setProject(null);
            }
        }

        return $this;
    }
}
