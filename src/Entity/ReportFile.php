<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportFileRepository")
 * @ORM\Table(name="activities_files")
 */
class ReportFile extends ActivityFile
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
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", inversedBy="activityFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="activityFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activity;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Report", inversedBy="reportFiles")
     */
    private $report;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

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

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(?Report $report): self
    {
        $this->report = $report;

        return $this;
    }
}
