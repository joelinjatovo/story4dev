<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AxeRepository")
 */
class Axe
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\graph", inversedBy="axes")
     */
    private $graph;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="axes")
     */
    private $author;

    public function __construct()
    {
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

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeInterface $start_at): self
    {
        $this->startAt = $start_at;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $end_at): self
    {
        $this->endAt = $end_at;

        return $this;
    }

    public function getGraph(): ?graph
    {
        return $this->graph;
    }

    public function setGraph(?graph $graph): self
    {
        $this->graph = $graph;

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
}
