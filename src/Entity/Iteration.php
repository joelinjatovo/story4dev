<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Traits\TimestampableEntity;
use App\Traits\SoftDeleteableEntity;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="App\Repository\IterationRepository")
 * @ORM\Table(name="iterations")
 */
class Iteration
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
     * @Groups({"full", "raw", "project", "goal"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "project", "goal"})
     */
    private $title;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="iterations")
     * @Gedmo\Versioned
     */
    private $author;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="iterations")
     * @Gedmo\Versioned
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Goal", mappedBy="iteration", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $goals;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function setId(int $id): ?self
    {
        $this->id = $id;
        
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
    
    public function hasGoals()
    {
        return $this->goals->count() > 0;
    }
    
    public function getGoals(): ?Collection
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
