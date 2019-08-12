<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

trait MetaTrait
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Meta\MetaProject")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $metas;
    
    public function getMetas(): Collection
    {
        return $this->metas;
    }

    public function addMeta(?Meta $meta): self
    {
        if (!$this->metas->contains($meta)) {
            $this->metas[] = $meta;
            $meta->setObject($this);
        }

        return $this;
    }

    public function removeMeta(?Meta $meta): self
    {
        if ($this->metas->contains($meta)) {
            $this->metas->removeElement($meta);
            if ($meta->getObject() === $this) {
                $meta->setObject(null);
            }
        }

        return $this;
    }
}
