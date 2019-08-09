<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MetaRepository")
 * @ORM\Table(name="metas")
 */
class Meta
{
    use \App\Traits\TimestampTrait;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $meta_key;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $meta_value;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $meta_type;

    /**
     * @ORM\Column(type="integer")
     */
    private $object_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMetaKey(): ?string
    {
        return $this->meta_key;
    }

    public function setMetaKey(string $meta_key): self
    {
        $this->meta_key = $meta_key;

        return $this;
    }

    public function getMetaValue(): ?string
    {
        return $this->meta_value;
    }

    public function setMetaValue(string $meta_value): self
    {
        $this->meta_value = $meta_value;

        return $this;
    }

    public function getMetaType(): ?string
    {
        return $this->meta_type;
    }

    public function setMetaType(string $meta_type): self
    {
        $this->meta_type = $meta_type;

        return $this;
    }

    public function getObjectId(): ?int
    {
        return $this->object_id;
    }

    public function setObjectId(int $object_id): self
    {
        $this->object_id = $object_id;

        return $this;
    }
}
