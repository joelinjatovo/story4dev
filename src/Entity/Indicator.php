<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\Meta\MetaIndicator;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IndicatorRepository")
 * @ORM\Table(name="indicators")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 */
class Indicator
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
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 1,
     *      maxMessage = "The title cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned
     */
    private $title;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projects")
     * @Gedmo\Versioned
     */
    private $author;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="indicators")
     * @Gedmo\Versioned
     */
    private $activity;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Unit", inversedBy="indicators")
     * @Gedmo\Versioned
     */
    private $unit;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Goal", mappedBy="indicator", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $goals;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Meta\MetaIndicator", mappedBy="indicator", orphanRemoval=true)
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $metas;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->goals = new ArrayCollection();
        $this->metas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $user): self
    {
        $this->author = $user;

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

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }
    
    public function getGoals(): ?Collection
    {
        return $this->goals;
    }

    public function addGoal(?Goal $goal): self
    {
        if (!$this->goals->contains($goal)) {
            $this->goals[] = $goal;
            $goal->setIndicator($this);
        }

        return $this;
    }

    public function removeGoal(?Goal $goal): self
    {
        if ($this->goals->contains($goal)) {
            $this->goals->removeElement($goal);
            if ($goal->getIndicator() === $this) {
                $goal->setIndicator(null);
            }
        }

        return $this;
    }
    
    public function getMetas(): Collection
    {
        return $this->metas;
    }

    public function addMeta(?MetaIndicator $meta): self
    {
        if (!$this->metas->contains($meta)) {
            $this->metas[] = $meta;
            $meta->setIndicator($this);
        }

        return $this;
    }

    public function removeMeta(?MetaIndicator $meta): self
    {
        if ($this->metas->contains($meta)) {
            $this->metas->removeElement($meta);
            if ($meta->getIndicator() === $this) {
                $meta->setIndicator(null);
            }
        }

        return $this;
    }
}
