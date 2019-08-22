<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as Serializer;

use App\Entity\Meta\ProjectMeta;
use App\Entity\Contribution\ProjectContribution;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ORM\Table(name="projects")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 */
class Project
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
     * @Groups({"full", "project", "raw"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "project", "raw"})
     */
    private $picture;

    /**
     * @Vich\UploadableField(mapping="default", fileNameProperty="picture")
     */
    private $pictureFile;

    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "The title cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned
     * @Groups({"full", "project", "raw"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "project", "raw"})
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "project", "raw"})
     */
    private $budget;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "project", "raw"})
     */
    private $start_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "project", "raw"})
     */
    private $end_at;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projects")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     * @Gedmo\Versioned
     * @Groups({"full", "project", "raw"})
     */
    private $author;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Periodicity", inversedBy="projects")
     * @Gedmo\Versioned
     * @Groups({"full", "project"})
     */
    private $periodicity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Activity", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Iteration", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $iterations;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Contribution\ProjectContribution", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $contributions;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Meta\ProjectMeta", mappedBy="project", orphanRemoval=true)
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $metas;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->activities = new ArrayCollection();
        $this->iterations = new ArrayCollection();
        $this->contributions = new ArrayCollection();
        $this->metas = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setPicture(?string $picture): void
    {
        $this->picture = $picture;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPictureFile(?File $pictureFile = null) : void
    {
        $this->pictureFile = $pictureFile;

        if (null !== $pictureFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getPictureFile() : ? File
    {
        return $this->pictureFile;
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $user): self
    {
        $this->author = $user;

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
    
    public function getActivities(): ?Collection
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
    
    public function getIterations(): ?Collection
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
    
    public function getContributions(): ?Collection
    {
        return $this->contributions;
    }

    public function addContribution(?ProjectContribution $contribution): self
    {
        if (!$this->contributions->contains($contribution)) {
            $this->contributions[] = $contribution;
            $contribution->setProject($this);
        }

        return $this;
    }

    public function removeContribution(?ProjectContribution $contribution): self
    {
        if ($this->contributions->contains($contribution)) {
            $this->contributions->removeElement($contribution);
            if ($contribution->getProject() === $this) {
                $contribution->setProject(null);
            }
        }

        return $this;
    }
    
    public function getMetas(): ?Collection
    {
        return $this->metas;
    }

    public function addMeta(?ProjectMeta $meta): self
    {
        if (!$this->metas->contains($meta)) {
            $this->metas[] = $meta;
            $meta->setProject($this);
        }

        return $this;
    }

    public function removeMeta(?ProjectMeta $meta): self
    {
        if ($this->metas->contains($meta)) {
            $this->metas->removeElement($meta);
            if ($meta->getProject() === $this) {
                $meta->setProject(null);
            }
        }

        return $this;
    }
}
