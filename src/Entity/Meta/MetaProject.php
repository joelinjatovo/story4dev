<?php

namespace App\Entity\Meta;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Project;

/**
 * @ORM\Entity
 */
class MetaProject extends BaseMeta
{
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="metas")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    private $project;

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
