<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IndicatorRepository")
 * @ORM\Table(name="indicators")
 */
class Indicator
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projects")
     */
    private $author;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="indicators")
     */
    private $activity;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Unit", inversedBy="indicators")
     */
    private $unit;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Goal", mappedBy="indicator", orphanRemoval=true)
     */
    private $goals;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->goals = new ArrayCollection();
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $user): self
    {
        $this->author = $user;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }
    
    public function getGoals(): Collection
    {
        return $this->goals;
    }

    public function addGoal(?Goal $goal): self
    {
        if (!$this->goals->contains($goal)) {
            $this->goals[] = $goal;
            $goal->setIndicator($this);
        }

        return $this;
    }

    public function removeGoal(?Goal $goal): self
    {
        if ($this->goals->contains($goal)) {
            $this->goals->removeElement($goal);
            if ($goal->getIndicator() === $this) {
                $goal->setIndicator(null);
            }
        }

        return $this;
    }
}
