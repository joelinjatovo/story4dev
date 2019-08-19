<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GoalRepository")
 * @ORM\Table(name="goals")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 */
class Goal
{
    
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;
    use SoftDeleteableEntity;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Gedmo\Versioned
     */
    private $value;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projects")
     * @Gedmo\Versioned
     */
    private $author;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Indicator", inversedBy="goals")
     * @Gedmo\Versioned
     */
    private $indicator;
    
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Iteration", inversedBy="goal")
     * @ORM\JoinColumn(nullable=true)
     * @Gedmo\Versioned
     */
    private $iteration;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $user): self
    {
        $this->author = $user;

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
