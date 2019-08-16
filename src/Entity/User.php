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

use App\Entity\Meta\MetaUser;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class User implements UserInterface, AdvancedUserInterface
{
    const STATUS_PING    = 'ping';
    const STATUS_ACTIVE  = 'active';
    const STATUS_BLOCKED = 'blocked';
    
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
     *      max = 100,
     *      maxMessage = "Your username cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $username;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     * @Assert\Length(
     *      max = 180,
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
     * @ORM\Column(name="fullname", type="string", length=100, nullable=true)
     */
    private $fullname;
    
    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "Your phone number cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="phone", type="string", length=50, nullable=true)
     */
    private $phone;
    
    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Your address cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="address", type="string", length=100, nullable=true)
     */
    private $address;
    
    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 10,
     *      maxMessage = "Your status cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="status", type="string", length=10, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @Assert\IsTrue
     */
    private $agree;

    /**
     * @ORM\Column(name="confirm_token", type="string", nullable=true)
     */
    private $confirmToken;

    /**
     * @ORM\Column(name="confirmed_at", type="datetime", nullable=true)
     */
    private $confirmedAt;

    /**
     * @ORM\Column(name="reset_token", type="string", nullable=true)
     */
    private $resetToken;

    /**
     * @ORM\Column(name="reseted_at", type="datetime", nullable=true)
     */
    private $resetedAt;

    /**
     * @ORM\Column(name="actived_at", type="datetime", nullable=true)
     */
    private $activedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Activity", mappedBy="author", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Indicator", mappedBy="author", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $indicators;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Iteration", mappedBy="author", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $iterations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Goal", mappedBy="author", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $goals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="author", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="author", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $reports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Result", mappedBy="author", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $results;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Contribution", mappedBy="user", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $contributions;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Meta\MetaUser", mappedBy="user", orphanRemoval=true)
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $metas;

    public function __construct()
    {
        $this->agree = true;
        $this->isActive = true;
        $this->setStatus(self::STATUS_PING);
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->activities = new ArrayCollection();
        $this->indicators = new ArrayCollection();
        $this->iterations = new ArrayCollection();
        $this->goals = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->results = new ArrayCollection();
        $this->contributions = new ArrayCollection();
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
    
    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }
    
    public function setConfirmToken(?string $token): self
    {
        $this->confirmToken = $token;
        
        return $this;
    }

    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?\DateTimeInterface $confirmedAt): self
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }
    
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }
    
    public function setResetToken(?string $token): self
    {
        $this->resetToken = $token;
        
        return $this;
    }

    public function getResetedAt(): ?\DateTimeInterface
    {
        return $this->resetedAt;
    }

    public function setResetedAt(?\DateTimeInterface $resetedAt): self
    {
        $this->resetedAt = $resetedAt;

        return $this;
    }

    public function getActivedAt(): ?\DateTimeInterface
    {
        return $this->activedAt;
    }

    public function setActivedAt(?\DateTimeInterface $activedAt): self
    {
        $this->activedAt = $activedAt;

        return $this;
    }
    
    public function getFullname(): ?string
    {
        return $this->fullname;
    }
    
    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;
        
        return $this;
    }  
    
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        
        return $this;
    } 
    
    public function getAddress(): ?string
    {
        return $this->address;
    }
    
    public function setAddress(string $address): self
    {
        $this->address = $address;
        
        return $this;
    }    

    public function getStatus(): string
    {
        return (string) $this->status;
    }
    
    public function setStatus(string $status): self
    {
        $this->status = $status;
        
        return $this;
    }
    
    public function isActive()
    {
        return $this->isActive;
    }

    public function setActive(bool $active): self
    {
        $this->isActive = $active;

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
    
    public function getActivities(): ?Collection
    {
        return $this->activities;
    }

    public function addActivity(?Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setAuthor($this);
        }

        return $this;
    }

    public function removeActivity(?Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            if ($activity->getAuthor() === $this) {
                $activity->setAuthor(null);
            }
        }

        return $this;
    }
    
    public function getIndicators(): Collection
    {
        return $this->indicators;
    }

    public function addIndicator(?Indicator $indicator): self
    {
        if (!$this->indicators->contains($indicator)) {
            $this->indicators[] = $indicator;
            $indicator->setAuthor($this);
        }

        return $this;
    }

    public function removeIndicator(?Indicator $indicator): self
    {
        if ($this->indicators->contains($indicator)) {
            $this->indicators->removeElement($indicator);
            if ($indicator->getAuthor() === $this) {
                $indicator->setAuthor(null);
            }
        }

        return $this;
    }
    
    public function getIterations(): ?Collection
    {
        return $this->iterations;
    }

    public function addIteration(?Iteration $iteration): self
    {
        if (!$this->iterations->contains($iteration)) {
            $this->iterations[] = $iteration;
            $iteration->setAuthor($this);
        }

        return $this;
    }

    public function removeIteration(?Iteration $iteration): self
    {
        if ($this->iterations->contains($iteration)) {
            $this->iterations->removeElement($iteration);
            if ($iteration->getAuthor() === $this) {
                $iteration->setAuthor(null);
            }
        }

        return $this;
    }
    
    public function getGoals(): ?Collection
    {
        return $this->goals;
    }

    public function addGoal(?Goal $goal): self
    {
        if (!$this->goals->contains($goal)) {
            $this->goals[] = $goal;
            $goal->setAuthor($this);
        }

        return $this;
    }

    public function removeGoal(?Goal $goal): self
    {
        if ($this->goals->contains($goal)) {
            $this->goals->removeElement($goal);
            if ($goal->getAuthor() === $this) {
                $goal->setAuthor(null);
            }
        }

        return $this;
    }
    
    public function getProjects(): ?Collection
    {
        return $this->projects;
    }

    public function addProject(?Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setAuthor($this);
        }

        return $this;
    }

    public function removeProject(?Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            if ($project->getAuthor() === $this) {
                $project->setAuthor(null);
            }
        }

        return $this;
    }
    
    public function getReports(): ?Collection
    {
        return $this->reports;
    }

    public function addReport(?Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setAuthor($this);
        }

        return $this;
    }

    public function removeReport(?Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            if ($report->getAuthor() === $this) {
                $report->setAuthor(null);
            }
        }

        return $this;
    }
    
    public function getResults(): ?Collection
    {
        return $this->results;
    }

    public function addResult(?Result $result): self
    {
        if (!$this->results->contains($result)) {
            $this->results[] = $result;
            $result->setAuthor($this);
        }

        return $this;
    }

    public function removeResult(?Result $result): self
    {
        if ($this->results->contains($result)) {
            $this->results->removeElement($result);
            if ($result->getAuthor() === $this) {
                $result->setAuthor(null);
            }
        }

        return $this;
    }
    
    public function getContributions(): ?Collection
    {
        return $this->contributions;
    }

    public function addContribution(?Contribution $contribution): self
    {
        if (!$this->contributions->contains($contribution)) {
            $this->contributions[] = $contribution;
            $contribution->setUser($this);
        }

        return $this;
    }

    public function removeContribution(?Contribution $contribution): self
    {
        if ($this->contributions->contains($contribution)) {
            $this->contributions->removeElement($contribution);
            if ($contribution->getUser() === $this) {
                $contribution->setUser(null);
            }
        }

        return $this;
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
