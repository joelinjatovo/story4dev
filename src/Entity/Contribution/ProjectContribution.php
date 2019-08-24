<?php

namespace App\Entity\Contribution;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Entity\User;
use App\Entity\Project;

/**
 * @ORM\Entity
 * @Gedmo\Loggable
 */
class ProjectContribution extends Base
{
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projectContributions")
     * @Gedmo\Versioned
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="contributions")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     * @Gedmo\Versioned
     * @Groups({"user"})
     */
    private $project;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
}
