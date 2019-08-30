<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Traits\LocationTrait;
use App\Traits\TimestampableEntity;
use App\Traits\SoftDeleteableEntity;

/**
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="App\Repository\ReportRepository")
 * @ORM\Table(name="reports")
 */
class Report
{
    
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;
    use SoftDeleteableEntity;
    
    /**
     * Location latitude,longitude, altitude fields
     */
    use LocationTrait;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "report"})
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "The title cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "report"})
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "report"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "report"})
     */
    private $synced_at;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reports")
     * @Gedmo\Versioned
     * @Groups({"full", "report"})
     */
    private $author;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="reports")
     * @Gedmo\Versioned
     * @Groups({"full", "report"})
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Result", mappedBy="report", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full", "report"})
     */
    private $results;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReportFile", mappedBy="report")
     */
    private $reportFiles;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->setSyncedAt(new \DateTime());
        $this->results = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->reportFiles = new ArrayCollection();
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSyncedAt(): ?\DateTimeInterface
    {
        return $this->synced_at;
    }

    public function setSyncedAt(?\DateTimeInterface $synced_at): self
    {
        $this->synced_at = $synced_at;

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
    
    public function getResults(): ?Collection
    {
        return $this->results;
    }

    public function addResult(?Result $result): self
    {
        if (!$this->results->contains($result)) {
            $this->results[] = $result;
            $result->setReport($this);
        }

        return $this;
    }

    public function removeResult(?Result $result): self
    {
        if ($this->results->contains($result)) {
            $this->results->removeElement($result);
            if ($result->getReport() === $this) {
                $result->setReport(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReportFile[]
     */
    public function getReportFiles(): Collection
    {
        return $this->reportFiles;
    }

    public function addReportFile(ReportFile $reportFile): self
    {
        if (!$this->reportFiles->contains($reportFile)) {
            $this->reportFiles[] = $reportFile;
            $reportFile->setReport($this);
        }

        return $this;
    }

    public function removeReportFile(ReportFile $reportFile): self
    {
        if ($this->reportFiles->contains($reportFile)) {
            $this->reportFiles->removeElement($reportFile);
            // set the owning side to null (unless already changed)
            if ($reportFile->getReport() === $this) {
                $reportFile->setReport(null);
            }
        }

        return $this;
    }
}
