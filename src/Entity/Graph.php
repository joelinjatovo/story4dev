<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GraphRepository")
 */
class Graph
{
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\project", inversedBy="graphs")
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="graphs")
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Axe", mappedBy="graph")
     */
    private $axes;

    public function __construct()
    {
        $this->axes = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }

    public function setId(?int $id): ?self
    {
        $this->id = $id;
        
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getProject(): ?project
    {
        return $this->project;
    }

    public function setProject(?project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getAuthor(): ?user
    {
        return $this->author;
    }

    public function setAuthor(?user $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Axe[]
     */
    public function getAxes(): Collection
    {
        return $this->axes;
    }

    public function addAxe(Axe $axe): self
    {
        if (!$this->axes->contains($axe)) {
            $this->axes[] = $axe;
            $axe->setGraph($this);
        }

        return $this;
    }

    public function removeAxe(Axe $axe): self
    {
        if ($this->axes->contains($axe)) {
            $this->axes->removeElement($axe);
            // set the owning side to null (unless already changed)
            if ($axe->getGraph() === $this) {
                $axe->setGraph(null);
            }
        }

        return $this;
    }
}
