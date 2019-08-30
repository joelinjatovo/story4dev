<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait ContactTrait
{

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full", "raw", "project", "activity"})
     */
    private $contactEmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full", "raw", "project", "activity"})
     */
    private $contactPhone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full", "raw", "project", "activity"})
     */
    private $contactAddress;

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $email): self
    {
        $this->contactEmail = $email;

        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): self
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    public function getContactAddress(): ?string
    {
        return $this->contactAddress;
    }

    public function setContactAddress(?string $contactAddress): self
    {
        $this->contactAddress = $contactAddress;

        return $this;
    }
}
