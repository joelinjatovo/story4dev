<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repositor\OptionRepository")
 * @ORM\Table(name="options")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class Option
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
     * @ORM\Column(type="string", length=255)
     */
    private $option_key;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $option_value;

    /**
     * @ORM\Column(type="boolean")
     */
    private $autoload;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOptionKey(): ?string
    {
        return $this->option_key;
    }

    public function setOptionKey(string $option_key): self
    {
        $this->option_key = $option_key;

        return $this;
    }

    public function getOptionValue(): ?string
    {
        return $this->option_value;
    }

    public function setOptionValue(string $option_value): self
    {
        $this->option_value = $option_value;

        return $this;
    }

    public function getAutoload(): ?bool
    {
        return $this->autoload;
    }

    public function setAutoload(bool $autoload): self
    {
        $this->autoload = $autoload;

        return $this;
    }
}
