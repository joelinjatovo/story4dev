<?php

namespace App\Entity\Token;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;

/**
 * @ORM\Entity
 */
class ResetToken extends BaseToken
{
    
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="reset_token")
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
