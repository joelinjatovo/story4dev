<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait LocationTrait
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full", "raw", "report", "result", "activity"})
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full", "raw", "report", "result", "activity"})
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full", "raw", "report", "result", "activity"})
     */
    private $altitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full", "raw", "report", "result", "activity"})
     */
    private $locationTitle;

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getAltitude(): ?string
    {
        return $this->altitude;
    }

    public function setAltitude(?string $altitude): self
    {
        $this->altitude = $altitude;

        return $this;
    }

    public function getLocationTitle(): ?string
    {
        return $this->locationTitle;
    }

    public function setLocationTitle(?string $title): self
    {
        $this->locationTitle = $title;

        return $this;
    }
}
