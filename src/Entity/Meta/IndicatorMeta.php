<?php

namespace App\Entity\Meta;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Indicator;

/**
 * @ORM\Entity
 */
class IndicatorMeta extends Base
{
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Indicator", inversedBy="metas")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    private $indicator;

    public function getIndicator(): ?Indicator
    {
        return $this->indicator;
    }

    public function setIndicator(?Indicator $indicator): self
    {
        $this->indicator = $indicator;

        return $this;
    }
}
