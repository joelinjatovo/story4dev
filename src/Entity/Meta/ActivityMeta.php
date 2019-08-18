<?php

namespace App\Entity\Meta;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Activity;

/**
 * @ORM\Entity
 */
class ActivityMeta extends Base
{
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="metas")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    private $activity;

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }
}
