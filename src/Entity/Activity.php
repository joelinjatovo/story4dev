<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityNotFoundException;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Traits\ContactTrait;
use App\Traits\TimestampableEntity;
use App\Traits\SoftDeleteableEntity;

use App\Entity\Meta\ActivityMeta;
use App\Entity\ActivityContribution;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 * @Gedmo\Loggable
 * @ORM\Entity(repositoryClass="App\Repository\ActivityRepository")
 * @ORM\Table(name="activities")
 */
class Activity
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
     * @Groups({"full", "raw", "activity", "project", "indicator", "report_feed"})
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "The title cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "activity", "project", "indicator", "report_feed"})
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "activity", "project", "indicator", "report_feed"})
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Gedmo\Versioned
     * @Assert\Type(
     *     type="float",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @Gedmo\Versioned
     * @Groups({"full", "raw", "activity", "project", "report_feed"})
     */
    private $budget;
    
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Address", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"full", "activity", "project"})
     */
    private $address;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="activities")
     * @ORM\JoinColumn(nullable=true)
     * @Gedmo\Versioned
     * @Groups({"full"})
     */
    private $author;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="activities")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="cascade")
     * @Gedmo\Versioned
     * @Groups({"full"})
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Indicator", mappedBy="activity", orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full", "activity", "project"})
     */
    private $indicators;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ActivityContribution", mappedBy="activity", orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full"})
     */
    private $contributions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ActivityFile", mappedBy="activity", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full", "activity", "project"})
     */
    private $activityFiles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Report", mappedBy="activity", cascade={"persist", "remove"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     * @Groups({"full", "activity", "project"})
     */
    private $reports;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Meta\ActivityMeta", mappedBy="activity", orphanRemoval=true)
     * @Groups({"meta_activity"})
     */
    protected $metas;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->indicators = new ArrayCollection();
        $this->contributions = new ArrayCollection();
        $this->metas = new ArrayCollection();
        $this->activityFiles = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBudget() : ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): self
    {
        $this->budget = $budget;

        return $this;
    }
    
    public function isDeleted()
    {
        return ! is_null( $this->deletedAt );
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

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
            $indicator->setActivity($this);
        }

        return $this;
    }

    public function removeIndicator(?Indicator $indicator): self
    {
        if ($this->indicators->contains($indicator)) {
            $this->indicators->removeElement($indicator);
            if ($indicator->getActivity() === $this) {
                $indicator->setActivity(null);
            }
        }

        return $this;
    }
    
    public function getContributions(): ?Collection
    {
        return $this->contributions;
    }

    public function addContribution(?ActivityContribution $contribution): self
    {
        if (!$this->contributions->contains($contribution)) {
            $this->contributions[] = $contribution;
            $contribution->setProject($this);
        }

        return $this;
    }

    public function removeContribution(?ActivityContribution $contribution): self
    {
        if ($this->contributions->contains($contribution)) {
            $this->contributions->removeElement($contribution);
            if ($contribution->getProject() === $this) {
                $contribution->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setActivity($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            // set the owning side to null (unless already changed)
            if ($report->getActivity() === $this) {
                $report->setActivity(null);
            }
        }

        return $this;
    }
    
    public function getMetas(): Collection
    {
        return $this->metas;
    }

    public function addMeta(?ActivityMeta $meta): self
    {
        if (!$this->metas->contains($meta)) {
            $this->metas[] = $meta;
            $meta->setActivity($this);
        }

        return $this;
    }

    public function removeMeta(?ActivityMeta $meta): self
    {
        if ($this->metas->contains($meta)) {
            $this->metas->removeElement($meta);
            if ($meta->getActivity() === $this) {
                $meta->setActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ActivityFile[]
     */
    public function getActivityFiles(): Collection
    {
        return $this->activityFiles;
    }

    public function addActivityFile(ActivityFile $activityFile): self
    {
        if (!$this->activityFiles->contains($activityFile)) {
            $this->activityFiles[] = $activityFile;
            $activityFile->setActivity($this);
        }

        return $this;
    }

    public function removeActivityFile(ActivityFile $activityFile): self
    {
        if ($this->activityFiles->contains($activityFile)) {
            $this->activityFiles->removeElement($activityFile);
            // set the owning side to null (unless already changed)
            if ($activityFile->getActivity() === $this) {
                $activityFile->setActivity(null);
            }
        }

        return $this;
    }

    public function getProgression(?Iteration $iteration = null)
    {
        $progression = 0;
        $indicators = $this->getIndicators();
        foreach($indicators as $indicator){
            $progression += $indicator->getProgression($iteration);
        }

        if(count($indicators)>0){
            return (int) ( $progression / count($indicators) );
        }
        
        return 0;
    }

    public function getGoalValue(?Iteration $iteration)
    {
        $goalValue = 0;
        foreach($this->getIndicators() as $indicator){
            $goalValue += $indicator->getGoalValue($iteration);
        }
        return $goalValue;
    }

    public function getValue(?Iteration $iteration)
    {
        $value = 0;
        foreach($this->getIndicators() as $indicator){
            $value += $indicator->getValue($iteration);
        }
        return $value;
    }

    public function getReportsCount(Iteration $iteration)
    {
        $value = 0;
        foreach($this->getReports() as $report){
            if(($report->getCreatedAt() >= $iteration->getStartAt()) && ($report->getCreatedAt() <= $iteration->getEndAt())){
                $value += 1;
            }
        }
        return $value;
    }
    
    public function getData()
    {
        $datas = [];
        try{        
            if($this->getProject()){
                foreach($this->getProject()->getIterations() as $iteration){
                    $data = [
                        "iteration"   => $iteration->getTitle(),
                        "value"       => $this->getValue($iteration),
                        "goal"        => $this->getGoalValue($iteration),
                        "report"      => $this->getReportsCount($iteration),
                        "progression" => $this->getProgression($iteration),
                    ];

                    foreach($this->getIndicators() as $indicator){
                        $data['i_'.$indicator->getId()] = $indicator->getProgression($iteration);
                    }

                    $datas[] = $data;
                }
            }
        }catch(EntityNotFoundException $e){}
        
        return $datas;
    }
    
    public function getSerie()
    {
        $series = [];
        foreach($this->getIndicators() as $indicator){
            $title = $indicator->getTitle();
            $series[] = [
                '_id'   => $indicator->getId(),
                'id'    => 'i_'.$indicator->getId(),
                'title' => $title,
            ];
        }
        return $series;
    }
}
