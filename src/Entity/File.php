<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File as SysFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @ORM\Table(name="files")
 */
class File
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"full", "raw", "file"})
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Name should not be blank.")
     * @ORM\Column(type="string", length=255)
     * @Groups({"full", "raw", "file"})
     */
    private $name;

    /**
     * @Assert\NotBlank(message="File should not be blank.")
     * @Assert\File(
     *     mimeTypes={"image/jpeg", "image/png", "image/gif", "application/x-gzip", "application/zip", "application/vnd.openxmlformats-officedocument.wordprocessingml.document"},
     *     maxSize="1074000000"
     * )
     * @Vich\UploadableField(mapping="default", fileNameProperty="name")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full", "raw", "file"})
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isExternal;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ActivityFile", mappedBy="file", orphanRemoval=true)
     */
    private $activityFiles;

    public function __construct()
    {
        $this->isExternal = false;
        $this->reportFiles = new ArrayCollection();
        $this->activityFiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFile(): ?SysFile
    {
        return $this->file;
    }

    public function setFile(?SysFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getIsExternal(): ?bool
    {
        return $this->isExternal;
    }

    public function setIsExternal(bool $isExternal): self
    {
        $this->isExternal = $isExternal;

        return $this;
    }

    /**
     * @return Collection|ActivityFile[]
     */
    public function getActivityFiles(): Collection
    {
        return $this->activityFiles;
    }

    public function addActivityFile(ActivityFile $activityFile): self
    {
        if (!$this->activityFiles->contains($activityFile)) {
            $this->activityFiles[] = $activityFile;
            $activityFile->setFile($this);
        }

        return $this;
    }

    public function removeActivityFile(ActivityFile $activityFile): self
    {
        if ($this->activityFiles->contains($activityFile)) {
            $this->activityFiles->removeElement($activityFile);
            // set the owning side to null (unless already changed)
            if ($activityFile->getFile() === $this) {
                $activityFile->setFile(null);
            }
        }

        return $this;
    }
}
