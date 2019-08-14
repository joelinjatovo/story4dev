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

use App\Entity\Meta\MetaActivity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityRepository")
 * @ORM\Table(name="activities")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 */
class Activity
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
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "The title cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned
     */
    private $title;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Gedmo\Versioned
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $budget;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projects")
     * @Gedmo\Versioned
     */
    private $author;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="activities")
     * @Gedmo\Versioned
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Indicator", mappedBy="activity", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $indicators;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Meta\MetaActivity", mappedBy="activity", orphanRemoval=true)
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $metas;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->indicators = new ArrayCollection();
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

    public function getBudget() : ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): self
    {
        $this->budget = $budget;

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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
    
    public function getIndicators(): Collection
    {
        return $this->indicators;
    }

    public function addIndicator(?Indicator $indicator): self
    {
        if (!$this->indicators->contains($indicator)) {
            $this->indicators[] = $indicator;
            $indicator->setActivity($this);
        }

        return $this;
    }

    public function removeIndicator(?Indicator $indicator): self
    {
        if ($this->indicators->contains($indicator)) {
            $this->indicators->removeElement($indicator);
            if ($indicator->getActivity() === $this) {
                $indicator->setActivity(null);
            }
        }

        return $this;
    }
    
    public function getMetas(): Collection
    {
        return $this->metas;
    }

    public function addMeta(?MetaActivity $meta): self
    {
        if (!$this->metas->contains($meta)) {
            $this->metas[] = $meta;
            $meta->setActivity($this);
        }

        return $this;
    }

    public function removeMeta(?MetaActivity $meta): self
    {
        if ($this->metas->contains($meta)) {
            $this->metas->removeElement($meta);
            if ($meta->getActivity() === $this) {
                $meta->setActivity(null);
            }
        }

        return $this;
    }
}
