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
 * @ORM\Entity(repositoryClass="App\Repository\ResultRepository")
 * @ORM\Table(name="results")
 */
class Result
{
    
    /**
     * Location latitude,longitude, altitude fields
     */
    use LocationTrait;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "report", "activity", "project"})
     */
    private $id;

    /**
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
     * @ORM\Column(type="integer", nullable=false)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "report", "activity", "project"})
     */
    private $value;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="results")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", onDelete="cascade")
     * @Gedmo\Versioned
     * @Groups({"full", "report"})
     */
    private $author;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Report", inversedBy="results")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="cascade")
     * @Gedmo\Versioned
     */
    private $report;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Indicator", inversedBy="goals")
     * @ORM\JoinColumn(name="indicator_id", referencedColumnName="id", onDelete="cascade")
     * @Gedmo\Versioned
     * @Groups({"full", "report", "activity", "project"})
     */
    private $indicator;

    public function setId(?int $id): ?self
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

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): self
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

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(?Report $report): self
    {
        $this->report = $report;

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
}
