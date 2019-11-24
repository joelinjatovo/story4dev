<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IndicatorFavoriteRepository")
 * @ORM\Table(name="indicators_favorites")
 */
class IndicatorFavorite
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="indicatorFavorites")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Indicator", inversedBy="indicatorFavorites")
     * @ORM\JoinColumn(name="indicator_id", referencedColumnName="id", onDelete="cascade")
     */
    private $indicator;

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

    public function getIndicator(): ?Indicator
    {
        return $this->indicator;
    }

    public function setIndicator(?Indicator $indicator): self
    {
        $this->indicator = $indicator;

        return $this;
    }
}
