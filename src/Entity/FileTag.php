<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileTagRepository")
 * @ORM\Table(name="files_tags")
 */
class FileTag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", inversedBy="filesTags")
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="cascade")
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tag", inversedBy="filesTags")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="cascade")
     */
    private $tag;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function setTag(?Tag $tag): self
    {
        $this->tag = $tag;

        return $this;
    }
}
