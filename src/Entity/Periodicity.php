<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Traits\TimestampableEntity;
use App\Traits\SoftDeleteableEntity;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="App\Repository\PeriodicityRepository")
 * @ORM\Table(name="periodicities")
 */
class Periodicity
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
     * @Groups({"full", "raw", "project"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "project"})
     */
    private $title;

    /**
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "project"})
     */
    private $delay;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projects")
     * @Gedmo\Versioned
     * @Groups({"full"})
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="periodicity", orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full"})
     */
    private $projects;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $user): self
    {
        $this->author = $user;

        return $this;
    }
    
    public function getProjects(): ?Collection
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
