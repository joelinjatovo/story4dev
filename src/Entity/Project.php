<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as Serializer;

use App\Entity\Meta\ProjectMeta;
use App\Entity\ProjectContribution;
use App\Traits\ContactTrait;
use App\Traits\TimestampableEntity;
use App\Traits\SoftDeleteableEntity;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ORM\Table(name="projects")
 */
class Project
{
    
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;
    use SoftDeleteableEntity;
    
    /**
     * Contact email,phone, address fields
     */
    use ContactTrait;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "project", "user", "activity", "report"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "project", "user", "report"})
     */
    private $picture;

    /**
     * @Vich\UploadableField(mapping="default", fileNameProperty="picture")
     */
    private $pictureFile;

    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "The title cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "project", "user", "activity", "report"})
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "project", "user", "activity", "report"})
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "project", "user", "activity", "report"})
     */
    private $budget;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "project", "user", "activity", "report"})
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "project", "user", "activity", "report"})
     */
    private $endAt;
    
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"full", "project"})
     */
    private $address;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projects")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     * @Gedmo\Versioned
     * @Groups({"full"})
     */
    private $author;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Periodicity", inversedBy="projects")
     * @Gedmo\Versioned
     * @Groups({"full", "project", "activity"})
     */
    private $periodicity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Activity", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full", "project"})
     */
    private $activities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Iteration", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full", "project"})
     */
    private $iterations;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectContribution", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full", "project"})
     */
    private $contributions;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Meta\ProjectMeta", mappedBy="project", orphanRemoval=true)
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     * @Groups({"full", "meta_project"})
     */
    protected $metas;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Unit", mappedBy="project")
     */
    private $units;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tag", mappedBy="project")
     */
    private $tags;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->activities = new ArrayCollection();
        $this->iterations = new ArrayCollection();
        $this->contributions = new ArrayCollection();
        $this->metas = new ArrayCollection();
        $this->units = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setPicture(?string $picture): void
    {
        $this->picture = $picture;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPictureFile(?File $pictureFile = null) : void
    {
        $this->pictureFile = $pictureFile;

        if (null !== $pictureFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getPictureFile() : ? File
    {
        return $this->pictureFile;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeInterface $start_at): self
    {
        $this->startAt = $start_at;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $end_at): self
    {
        $this->endAt = $end_at;

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $user): self
    {
        $this->author = $user;

        return $this;
    }

    public function getPeriodicity(): ?Periodicity
    {
        return $this->periodicity;
    }

    public function setPeriodicity(?Periodicity $periodicity): self
    {
        $this->periodicity = $periodicity;

        return $this;
    }
    
    public function getActivities(): ?Collection
    {
        return $this->activities;
    }

    public function addActivity(?Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setProject($this);
        }

        return $this;
    }

    public function removeActivity(?Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            if ($activity->getProject() === $this) {
                $activity->setProject(null);
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
            $iteration->setProject($this);
        }

        return $this;
    }

    public function removeIteration(?Iteration $iteration): self
    {
        if ($this->iterations->contains($iteration)) {
            $this->iterations->removeElement($iteration);
            if ($iteration->getProject() === $this) {
                $iteration->setProject(null);
            }
        }

        return $this;
    }
    
    public function getContributions(): ?Collection
    {
        return $this->contributions;
    }

    public function addContribution(?ProjectContribution $contribution): self
    {
        if (!$this->contributions->contains($contribution)) {
            $this->contributions[] = $contribution;
            $contribution->setProject($this);
        }

        return $this;
    }

    public function removeContribution(?ProjectContribution $contribution): self
    {
        if ($this->contributions->contains($contribution)) {
            $this->contributions->removeElement($contribution);
            if ($contribution->getProject() === $this) {
                $contribution->setProject(null);
            }
        }

        return $this;
    }
    
    public function getMetas(): ?Collection
    {
        return $this->metas;
    }

    public function addMeta(?ProjectMeta $meta): self
    {
        if (!$this->metas->contains($meta)) {
            $this->metas[] = $meta;
            $meta->setProject($this);
        }

        return $this;
    }

    public function removeMeta(?ProjectMeta $meta): self
    {
        if ($this->metas->contains($meta)) {
            $this->metas->removeElement($meta);
            if ($meta->getProject() === $this) {
                $meta->setProject(null);
            }
        }

        return $this;
    }
    
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->title,
            $this->description,
        ]);
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        
        list(
            $this->id,
            $this->title,
            $this->description
        ) = $data;
    }

    /**
     * @return Collection|Unit[]
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(Unit $unit): self
    {
        if (!$this->units->contains($unit)) {
            $this->units[] = $unit;
            $unit->setProject($this);
        }

        return $this;
    }

    public function removeUnit(Unit $unit): self
    {
        if ($this->units->contains($unit)) {
            $this->units->removeElement($unit);
            // set the owning side to null (unless already changed)
            if ($unit->getProject() === $this) {
                $unit->setProject(null);
            }
        }

        return $this;
    }

    public function getProgression(?Iteration $iteration = null)
    {
        $progression = 0;
        $activities = $this->getActivities();
        foreach($activities as $activity){
            $progression += $activity->getProgression($iteration);
        }

        if(count($activities)>0){
            return (int) ( $progression / count($activities) );
        }
        
        return 0;
    }

    public function getGoalValue(?Iteration $iteration = null)
    {
        $value = 0;
        if($iteration){
            foreach($iteration->getGoals() as $goal){
                $value += $goal->getValue();
            }
        }else{
            foreach($this->getIterations() as $iteration){
                foreach($iteration->getGoals() as $goal){
                    $value += $goal->getValue();
                }
            }
        }
        return $value;
    }

    public function getValue(?Iteration $iteration = null)
    {
        $value = 0;
        foreach($this->getActivities() as $activity){
            $value += $activity->getValue($iteration);
        }
        return $value;
    }

    public function getReportsCount(Iteration $iteration)
    {
        $value = 0;
        foreach($this->getActivities() as $activity){
            $value += $activity->getReportsCount($iteration);
        }
        return $value;
    }
    
    public function getData()
    {
        $datas = [];
        foreach($this->getIterations() as $iteration){
            $data = [
                "iteration"   => $iteration->getTitle(),
                "value"       => $this->getValue($iteration),
                "goal"        => $this->getGoalValue($iteration),
                "report"      => $this->getReportsCount($iteration),
                "progression" => $this->getProgression($iteration),
            ];
            
            foreach($this->getActivities() as $activity){
                $data['activity_'.$activity->getId()] = $activity->getProgression($iteration);
            }
            
            $datas[] = $data;
        }
        
        return $datas;
    }
    
    public function getSerie()
    {
        $series = [];
        foreach($this->getActivities() as $activity){
            $series[] = [
                'id'    => 'activity_'.$activity->getId(),
                'title' => $activity->getTitle(),
            ];
        }
        return $series;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->setProject($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            // set the owning side to null (unless already changed)
            if ($tag->getProject() === $this) {
                $tag->setProject(null);
            }
        }

        return $this;
    }
}
