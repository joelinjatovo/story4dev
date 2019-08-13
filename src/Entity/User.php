<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class User implements UserInterface, AdvancedUserInterface
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
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Your username must be at least {{ limit }} characters long",
     *      maxMessage = "Your username cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Your email cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = 5,
     *      minMessage = "Your password must be at least {{ limit }} characters long",
     * )
     * @ORM\Column(type="string")
     */
    private $password;
    
    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Your fullname cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="full_name", type="string", length=255, nullable=true)
     */
    private $full_name;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @Assert\IsTrue
     */
    private $agree;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Meta\MetaUser", mappedBy="user", orphanRemoval=true)
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $metas;

    public function __construct()
    {
        $this->agree = true;
        $this->isActive = true;
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    
    public function getFullName(): ?string
    {
        return $this->full_name;
    }
    
    public function setFullName(string $fullName): self
    {
        $this->full_name = $fullName;
        
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    } 
    
    public function isAgree()
    {
        return $this->agree;
    }

    public function setAgree(bool $agree): self
    {
        $this->agree = $agree;

        return $this;
    }    
    
    /**
     * @see AdvancedUserInterface
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @see AdvancedUserInterface
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @see AdvancedUserInterface
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }
    
    /**
     * @see AdvancedUserInterface
     */
    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    
    public function getMetas(): ?Collection
    {
        return $this->metas;
    }

    public function addMeta(?MetaUser $meta): self
    {
        if (!$this->metas->contains($meta)) {
            $this->metas[] = $meta;
            $meta->setUser($this);
        }

        return $this;
    }

    public function removeMeta(?MetaUser $meta): self
    {
        if ($this->metas->contains($meta)) {
            $this->metas->removeElement($meta);
            if ($meta->getUser() === $this) {
                $meta->setUser(null);
            }
        }

        return $this;
    }
}
