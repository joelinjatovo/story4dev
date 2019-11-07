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
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="App\Repository\ReportRepository")
 * @ORM\Table(name="reports")
 */
class Report
{
    const STATUS_DRAFT      = 'draft';
    const STATUS_OPENED     = 'opened';
    const STATUS_CLOSED     = 'closed';
    const STATUS_TERMINATED = 'terminated';
    
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
     * @Groups({"full", "raw", "report", "activity", "project", "report_feed"})
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
     * @Groups({"full", "raw", "report", "activity", "project", "report_feed"})
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "report", "activity", "project", "report_feed"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "report", "activity", "project", "report_feed"})
     */
    private $synced_at;
    
    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 10,
     *      maxMessage = "Your status cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="status", type="string", length=10, nullable=true)
     * @Groups({"full", "report", "activity", "project"})
     */
    private $status;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reports")
     * @ORM\JoinColumn(nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "report", "activity", "project"})
     */
    private $author;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="reports")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id", onDelete="cascade")
     * @Gedmo\Versioned
     * @Groups({"full", "report", "report_feed"})
     */
    private $activity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Result", mappedBy="report", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full", "report", "activity", "project", "report_feed"})
     */
    private $results;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ReportFile", mappedBy="report", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full", "report", "activity", "project", "report_feed"})
     */
    private $reportFiles;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"full", "report", "activity", "project"})
     */
    private $isModified;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups({"full", "report", "activity", "project"})
     */
    private $ip;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"full", "report", "activity", "project"})
     */
    private $publishExternally;

    public function __construct()
    {
        $this->publishExternally = false;
        $this->isModified = false;
        $this->setStatus(self::STATUS_OPENED);
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

    public function getStatus(): ?string
    {
        return (string) $this->status;
    }
    
    public function setStatus(?string $status): self
    {
        $this->status = $status;
        
        return $this;
    }    

    public function getStatusClass(): ?string
    {
        return $this->isClosed() ? 'warning' : ( $this->isTerminated() ? 'danger' : 'success' );
    }      

    public function getStatusLabel(): ?string
    {
        return $this->isClosed() ? 'Clôturé' : ( $this->isTerminated() ? 'Términé' : 'Editable' );
    }  
    
    public function isOpened()
    {
        return $this->getStatus() == self::STATUS_OPENED;
    }
    
    public function isClosed()
    {
        return $this->getStatus() == self::STATUS_CLOSED;
    }
    
    public function isTerminated()
    {
        return $this->getStatus() == self::STATUS_TERMINATED;
    }
    
    public function open(): self
    {
        $this->setStatus(self::STATUS_OPENED);
        
        return $this;
    }
    
    public function close(): self
    {
        $this->setStatus(self::STATUS_CLOSED);
        
        return $this;
    }
    
    public function isDeleted()
    {
        return ! is_null( $this->deletedAt );
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

    public function getIsModified(): ?bool
    {
        return $this->isModified;
    }

    public function setIsModified(bool $isModified): self
    {
        $this->isModified = $isModified;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getPublishExternally(): ?bool
    {
        return $this->publishExternally;
    }

    public function setPublishExternally(?bool $publishExternally): self
    {
        $this->publishExternally = $publishExternally;

        return $this;
    }
}
