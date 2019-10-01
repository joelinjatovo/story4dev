<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Entity\Project;
use App\Entity\User;

/**
 * @ORM\Table(name="project_contributions")
 * @ORM\Entity(repositoryClass="App\Repository\ProjectContributionRepository")
 * @Gedmo\Loggable
 */
class ProjectContribution
{
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;
    use SoftDeleteableEntity;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned
     * @Groups({"user"})
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     * @Gedmo\Versioned
     * @Groups({"user", "project", "activity"})
     */
    private $roles = [];
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projectContributions")
     * @Gedmo\Versioned
     * @Groups({"project"})
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="contributions")
     * @Gedmo\Versioned
     * @Groups({"user"})
     */
    private $project;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_CONTRIBUTOR';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

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
