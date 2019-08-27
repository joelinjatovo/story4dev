<?php

namespace App\Entity\Contribution;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Entity\Activity;
use App\Entity\User;

/**
 * @ORM\Entity
 * @Gedmo\Loggable
 */
class ActivityContribution extends Base
{
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="activityContributions")
     * @Gedmo\Versioned
     * @Groups({"activity"})
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="contributions")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     * @Gedmo\Versioned
     * @Groups({"user"})
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
