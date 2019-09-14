<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityFileRepository")
 * @ORM\Table(name="activities_files")
 */
class ActivityFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"full", "activity", "project"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"full", "activity", "project"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File", inversedBy="activityFiles")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"full", "activity", "project"})
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="activityFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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
