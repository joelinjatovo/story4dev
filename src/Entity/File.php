<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File as SysFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @Vich\Uploadable
 */
class File
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Name should not be blank.")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @Assert\NotBlank(message="File should not be blank.")
     * @Assert\File(
     *     mimeTypes={"image/jpeg", "image/png", "image/gif", "application/x-gzip", "application/zip"},
     *     maxSize="1074000000"
     * )
     * @Vich\UploadableField(mapping="default", fileNameProperty="name")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

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
}
