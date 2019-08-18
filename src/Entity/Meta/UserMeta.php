<?php

namespace App\Entity\Meta;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;

/**
 * @ORM\Entity
 */
class UserMeta extends Base
{
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="options")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    private $user;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
