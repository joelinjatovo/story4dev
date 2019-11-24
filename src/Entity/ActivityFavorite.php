<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityFavoriteRepository")
 * @ORM\Table(name="activities_favorites")
 */
class ActivityFavorite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="activityFavorites")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="activityFavorites")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id", onDelete="cascade")
     */
    private $activity;

    public function getId(): ?int
    {
        return $this->id;
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
