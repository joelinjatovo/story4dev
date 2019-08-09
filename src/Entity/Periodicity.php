<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PeriodicityRepository")
 * @ORM\Table(name="perioicities")
 */
class Periodicity
{
    use \App\Traits\TimestampTrait;
    
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
     * @ORM\Column(type="integer")
     */
    private $delay;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="periodicity", orphanRemoval=true)
     */
    private $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
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

    public function getDelay(): ?int
    {
        return $this->delay;
    }

    public function setDelay(int $delay): self
    {
        $this->delay = $delay;

        return $this;
    }
    
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(?Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setPeriodicity($this);
        }

        return $this;
    }

    public function removeProject(?Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            if ($project->getPeriodicity() === $this) {
                $project->setPeriodicity(null);
            }
        }

        return $this;
    }
}
