<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportFileRepository")
 * @ORM\Table(name="reports_files")
 */
class ReportFile 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"full", "activity", "project", "report", "report_feed"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"full", "activity", "project", "report", "report_feed"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", inversedBy="activityFiles")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="cascade")
     * @Groups({"full", "activity", "project", "report", "report_feed"})
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="activityFiles")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id", onDelete="cascade", nullable=true)
     */
    private $activity;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Report", inversedBy="reportFiles")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="cascade")
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
