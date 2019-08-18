<?php

namespace App\Entity\Contribution;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use App\Entity\User;
use App\Entity\Activity;

/**
 * @ORM\Entity
 * @Gedmo\Loggable
 */
class ActivityContribution extends Base
{
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="activityContributions")
     * @Gedmo\Versioned
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="contributions")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     * @Gedmo\Versioned
     */
    private $activity;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
}
