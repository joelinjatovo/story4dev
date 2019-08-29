<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class ReportFile extends ActivityFile
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Report", inversedBy="reportFiles")
     */
    private $report;

    public function getId(): ?int
    {
        return $this->id;
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
