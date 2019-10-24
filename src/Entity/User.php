<?php

namespace App\Entity;

use App\Entity\Meta\UserMeta;
use App\Entity\ProjectContribution;
use App\Entity\ActivityContribution;
use App\Traits\TimestampableEntity;
use App\Traits\SoftDeleteableEntity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User implements UserInterface, \Serializable
{
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;
    use SoftDeleteableEntity;
    
    const STATUS_PING     = 'ping';
    const STATUS_ACTIVE   = 'active';
    const STATUS_CANCELED = 'canceled';
    const STATUS_BLOCKED  = 'blocked';
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"full", "raw", "project", "user", "activity", "goal", "report"})
     */
    private $id;
    
    /**
     * @ORM\Column(name="facebook_id",type="string", nullable=true)
     * @Groups({"full", "raw", "project", "user", "report"})
     */
    protected $facebookId;

    /**
     * @ORM\Column(name="google_id", type="string", nullable=true)
     * @Groups({"full", "raw", "project", "user", "report"})
     */
    protected $googleId;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"full", "raw", "project", "user", "report"})
     */
    private $avatar;

    /**
     * @Vich\UploadableField(mapping="default", fileNameProperty="avatar")
     */
    private $avatarFile;

    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Your username cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=100, unique=true)
     * @Groups({"full", "raw", "project", "user", "report"})
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
     * @Groups({"full", "raw", "project", "user", "report"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"full", "raw", "project", "user", "report"})
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
     * @Gedmo\Slug(fields={"username"})
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     * @Groups({"full", "raw", "project", "user", "report"})
     */
    private $slug;

    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 2,
     *      maxMessage = "Language cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=2, nullable=true)
     * @Groups({"full", "raw", "project", "user", "report"})
     */
    private $language;
    
    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Your fullname cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="full_name", type="string", length=100, nullable=true)
     * @Groups({"full", "project", "user", "report"})
     */
    private $fullName;
    
    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Your title cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="title", type="string", length=100, nullable=true)
     * @Groups({"full", "project", "user", "report"})
     */
    private $title;
    
    /**
     * @Assert\Type("string")
     * @ORM\Column(name="presentation", type="string", nullable=true)
     * @Groups({"full", "project", "user", "report"})
     */
    private $presentation;
    
    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Company name cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="company", type="string", length=100, nullable=true)
     * @Groups({"full", "project", "user", "report"})
     */
    private $company;
    
    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "Your phone number cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="phone", type="string", length=50, nullable=true)
     * @Groups({"full", "project", "user", "report"})
     */
    private $phone;
    
    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "Your web site cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="website", type="string", length=50, nullable=true)
     * @Groups({"full", "project", "user", "report"})
     */
    private $website;
    
    /**
     * @Assert\Type("string")
     * @Assert\Length(
     *      max = 10,
     *      maxMessage = "Your status cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(name="status", type="string", length=10, nullable=true)
     * @Groups({"full"})
     */
    private $status;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     * @Groups({"full"})
     */
    private $isActive;

    /**
     * @ORM\Column(name="is_verified", type="boolean")
     * @Groups({"full"})
     */
    private $isVerified;

    /**
     * Used in form to check if he was accepted terms and conditions
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
     * @ORM\OneToOne(targetEntity="App\Entity\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"user", "project"})
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Activity", mappedBy="author", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Indicator", mappedBy="author", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $indicators;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Iteration", mappedBy="author", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $iterations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Goal", mappedBy="author", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $goals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="author", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="author", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $reports;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Result", mappedBy="author", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $results;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Token", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $tokens;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectContribution", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full", "user"})
     */
    private $projectContributions;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ActivityContribution", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full", "user"})
     */
    private $activityContributions;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Meta\UserMeta", mappedBy="user", orphanRemoval=true)
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $metas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\File", mappedBy="author", cascade={"persist", "remove"})
     */
    private $files;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Download", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $downloads;

    public function __construct()
    {
        $this->agree = true;
        $this->isActive = true;
        $this->isVerified = false;
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
        $this->projectContributions = new ArrayCollection();
        $this->activityContributions = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->downloads = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFacebookId()
    {
        return $this->facebookId;
    }

    public function setFacebookId($facebookId): self
    {
        $this->facebookId = $facebookId;
        
        return $this;
    }

    public function getGoogleId()
    {
        return $this->googleId;
    }

    public function setGoogleId($googleId): self
    {
        $this->googleId = $googleId;
        
        return $this;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;
        
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatarFile(?File $avatarFile) : self
    {
        $this->avatarFile = $avatarFile;

        if (null !== $avatarFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        
        return $this;
    }

    public function getAvatarFile() : ? File
    {
        return $this->avatarFile;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        
        return $this;
    }  

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }  

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(string $presentation): self
    {
        $this->presentation = $presentation;

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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug)
    {
        $this->slug = $slug;

        return $this;
    }
    
    public function getLanguage(): ?string
    {
        return $this->language;
    }
    
    public function setLanguage(?string $language): self
    {
        $this->language = $language;
        
        return $this;
    } 
    
    public function getFullName(): ?string
    {
        return $this->fullName;
    }
    
    public function setFullName(?string $fullname): self
    {
        $this->fullName = $fullname;
        
        return $this;
    }  
    
    public function getCompany(): ?string
    {
        return $this->company;
    }
    
    public function setCompany(string $company): self
    {
        $this->company = $company;
        
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
    
    public function getWebsite(): ?string
    {
        return $this->website;
    }
    
    public function setWebsite(string $website): self
    {
        $this->website = $website;
        
        return $this;
    } 
    
    public function getAddress(): ?Address
    {
        return $this->address;
    }
    
    public function setAddress(?Address $address): self
    {
        $this->address = $address;
        
        return $this;
    }    

    public function getStatusClass(): ?string
    {
        return $this->isCanceled() ? 'warning': ( $this->isBlocked() ? 'danger' : ( $this->isPinged() ? 'brand' : 'success' ) );
    }    

    public function getStatusLabel(): ?string
    {
        if( $this->isDeleted() ){
            return "Supprimé";
        }
        if( $this->isCanceled() ){
            return "Réfusé";
        }
        if( $this->isBlocked() ){
            return "Bloqué";
        }
        if( $this->isPinged() ){
            return "En attennte de validation";
        }

        return "Actif";
    }

    public function getStatus(): ?string
    {
        return (string) $this->status;
    }
    
    public function setStatus(?string $status): self
    {
        $this->status = $status;
        
        if( $status == self::STATUS_ACTIVE){
            $this->setIsActive(true);
        }else{
            $this->setIsActive(false);
        }
        
        return $this;
    }
    
    public function isPinged()
    {
        return $this->getStatus() == self::STATUS_PING;
    }
    
    public function isBlocked()
    {
        return $this->getStatus() == self::STATUS_BLOCKED;
    }
    
    public function isCanceled()
    {
        return $this->getStatus() == self::STATUS_CANCELED;
    }
    
    public function isActive()
    {
        return $this->isActive;
    }

    public function setIsActive(bool $active): self
    {
        $this->isActive = $active;

        return $this;
    }

    public function setActive(bool $active): self
    {
        $this->isActive = $active;

        return $this;
    }
    
    public function isVerified()
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $verified): self
    {
        return $this->setVerified($verified);
    }

    public function setVerified(bool $verified): self
    {
        $this->isVerified = $verified;

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
    
    public function isDeleted()
    {
        return ! is_null( $this->deletedAt );
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
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
    
    public function getTokens(): ?Collection
    {
        return $this->tokens;
    }

    public function addToken(?Token $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
            $token->setUser($this);
        }

        return $this;
    }

    public function removeToken(?Token $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
            if ($token->getUser() === $this) {
                $token->setUser(null);
            }
        }

        return $this;
    }
    
    public function getProjectContributions(): ?Collection
    {
        return $this->projectContributions;
    }

    public function addProjectContribution(?ProjectContribution $contribution): self
    {
        if (!$this->projectContributions->contains($contribution)) {
            $this->projectContributions[] = $contribution;
            $contribution->setUser($this);
        }

        return $this;
    }

    public function removeProjectContribution(?ProjectContribution $contribution): self
    {
        if ($this->projectContributions->contains($contribution)) {
            $this->projectContributions->removeElement($contribution);
            if ($contribution->getUser() === $this) {
                $contribution->setUser(null);
            }
        }

        return $this;
    }
    
    public function getActivityContributions(): ?Collection
    {
        return $this->activityContributions;
    }

    public function addActivityContribution(?ActivityContribution $contribution): self
    {
        if (!$this->activityContributions->contains($contribution)) {
            $this->activityContributions[] = $contribution;
            $contribution->setUser($this);
        }

        return $this;
    }

    public function removeActivityContribution(?ActivityContribution $contribution): self
    {
        if ($this->activityContributions->contains($contribution)) {
            $this->activityContributions->removeElement($contribution);
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
    
    public function getInitials(): ?string
    {
        $name = $this->getFullname();
        if( empty( $name ) ) {
            $name = $this->getUsername();
        }
        
        $words = explode(" ", $name);
        $initials = null;
        
        if( count( $words ) > 1){
            foreach ($words as $w) {
                 $initials .= $w[0];
            }
            
            $initials = mb_substr( $initials, 0, 2, "UTF-8" );
        } else {
            if( strlen( $name ) > 2 ) {
                $initials =  mb_substr( $name, 0, 2, "UTF-8" );
            } else {
                $initials = $name;
            }
            
        }
        return strtoupper( $initials ); //JB
    }

    public function addMeta(?UserMeta $meta): self
    {
        if (!$this->metas->contains($meta)) {
            $this->metas[] = $meta;
            $meta->setUser($this);
        }

        return $this;
    }

    public function removeMeta(?UserMeta $meta): self
    {
        if ($this->metas->contains($meta)) {
            $this->metas->removeElement($meta);
            if ($meta->getUser() === $this) {
                $meta->setUser(null);
            }
        }

        return $this;
    }

    public function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    public function isSuperAdmin()
    {
        return in_array('ROLE_SUPER_ADMIN', $this->getRoles());
    }
    
    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->email,
            $this->password,
            // $this->salt
        ) = unserialize($serialized);
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setAuthor($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getAuthor() === $this) {
                $file->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Download[]
     */
    public function getDownloads(): Collection
    {
        return $this->downloads;
    }

    public function addDownload(Download $download): self
    {
        if (!$this->downloads->contains($download)) {
            $this->downloads[] = $download;
            $download->setUser($this);
        }

        return $this;
    }

    public function removeDownload(Download $download): self
    {
        if ($this->downloads->contains($download)) {
            $this->downloads->removeElement($download);
            // set the owning side to null (unless already changed)
            if ($download->getUser() === $this) {
                $download->setUser(null);
            }
        }

        return $this;
    }
}
