<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 * @ORM\Table(name="tags")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FileTag", mappedBy="tag")
     */
    private $fileTags;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="tags")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="cascade")
     */
    private $project;

    public function __construct()
    {
        $this->fileTags = new ArrayCollection();
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

    /**
     * @return Collection|FileTag[]
     */
    public function getFileTags(): Collection
    {
        return $this->fileTags;
    }

    public function addFileTag(FileTag $fileTag): self
    {
        if (!$this->fileTags->contains($fileTag)) {
            $this->fileTags[] = $fileTag;
            $fileTag->setTag($this);
        }

        return $this;
    }

    public function removeFileTag(FileTag $fileTag): self
    {
        if ($this->fileTags->contains($fileTag)) {
            $this->fileTags->removeElement($fileTag);
            // set the owning side to null (unless already changed)
            if ($fileTag->getTag() === $this) {
                $fileTag->setTag(null);
            }
        }

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
