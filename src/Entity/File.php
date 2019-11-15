<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as SysFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Traits\TimestampableEntity;

/**
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @ORM\Table(name="files")
 */
class File
{
    
    /**
     * Hook timestampable behavior
     * updates createdAt, updatedAt fields
     */
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"full", "raw", "file", "activity", "project", "report", "report_feed"})
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Name should not be blank.")
     * @ORM\Column(type="string", length=255)
     * @Groups({"full", "raw", "file", "activity", "project", "report", "report_feed"})
     */
    private $name;

    /**
     * @Assert\NotBlank(message="File should not be blank.")
     * @Assert\File(
     *     mimeTypes={
            "image/jpeg",
            "image/png",
            "image/gif",
            "application/pdf",
            "application/msword",
            "application/vnd.ms-powerpoint",
            "application/vnd.ms-excel",
            "application/vnd.openxmlformats-officedocument.presentationml.presentation",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
        },
     *     maxSize="1074000000"
     * )
     * @Vich\UploadableField(mapping="default", fileNameProperty="name")
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full", "raw", "file", "activity", "project", "report", "report_feed"})
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full", "raw", "file", "activity", "project", "report", "report_feed"})
     */
    private $url;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"full", "raw", "file", "activity", "project", "report", "report_feed"})
     */
    private $isExternal;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ActivityFile", mappedBy="file", orphanRemoval=true)
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id", onDelete="cascade")
     */
    private $activityFiles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mimeType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="files")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"full", "raw", "file", "activity", "project", "report", "report_feed"})
     */
    private $displayName;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"full", "raw", "file", "activity", "project", "report", "report_feed"})
     */
    private $isInstantly;

    /**
     * @ORM\Column(type="datetime")
     */
    private $syncedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Download", mappedBy="file")
     */
    private $downloads;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FileTag", mappedBy="file")
     */
    private $fileTags;

    public function __construct()
    {
        $this->isExternal = false;
        $this->isInstantly = false;
        $this->reportFiles = new ArrayCollection();
        $this->activityFiles = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->setSyncedAt(new \DateTime());
        $this->downloads = new ArrayCollection();
        $this->fileTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFile(): ?SysFile
    {
        return $this->file;
    }

    public function setFile(?SysFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getIsExternal(): ?bool
    {
        return $this->isExternal;
    }

    public function setIsExternal(bool $isExternal): self
    {
        $this->isExternal = $isExternal;

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
            $activityFile->setFile($this);
        }

        return $this;
    }

    public function removeActivityFile(ActivityFile $activityFile): self
    {
        if ($this->activityFiles->contains($activityFile)) {
            $this->activityFiles->removeElement($activityFile);
            // set the owning side to null (unless already changed)
            if ($activityFile->getFile() === $this) {
                $activityFile->setFile(null);
            }
        }

        return $this;
    }

    public function getMimeTypes()
    {
        return array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js'   => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpg' => 'image/jpg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt'  => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'rtf'  => 'application/rtf',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
        
    }
    
    public function isImage()
    {
        $type = 'file';
        if( in_array( $this->getMimeType(), $this->getMimeTypes() ) ){
            $type = array_search($this->getMimeType(), $this->getMimeTypes());
        }
        
        if( in_array( $type, ['jpg', 'jpeg', 'png', 'gif']) ) {
             return true;
        }
           
        return false;
    }
    
    public function getIcone(): ?string
    {
        $type = 'file';
        if( in_array( $this->getMimeType(), $this->getMimeTypes() ) ){
            $type = array_search($this->getMimeType(), $this->getMimeTypes());
        }
        
        if( in_array( $type, ['jpg', 'jpeg', 'png', 'gif']) ) {
             return '/uploads/file/'.$this->getName();
        }
        
        if( in_array( $type, ['pdf', 'xml', 'csv', 'html', 'javascript', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'mp3', 'zip', 'mp4'] ) ){
            return 'images/icon/'.$type.'.svg';
        }
           
        return 'images/icon/file.svg';
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getIsInstantly(): ?bool
    {
        return $this->isInstantly;
    }

    public function setIsInstantly(?bool $isInstantly): self
    {
        $this->isInstantly = $isInstantly;

        return $this;
    }

    public function getSyncedAt(): ?\DateTimeInterface
    {
        return $this->syncedAt;
    }

    public function setSyncedAt(\DateTimeInterface $syncedAt): self
    {
        $this->syncedAt = $syncedAt;

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
            $download->setFile($this);
        }

        return $this;
    }

    public function removeDownload(Download $download): self
    {
        if ($this->downloads->contains($download)) {
            $this->downloads->removeElement($download);
            // set the owning side to null (unless already changed)
            if ($download->getFile() === $this) {
                $download->setFile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FileTag[]
     */
    public function getFileTags(): Collection
    {
        return $this->fileTags;
    }

    public function addFileTag(FileTag $fileTag): self
    {
        if (!$this->fileTags->contains($fileTag)) {
            $this->fileTags[] = $fileTag;
            $fileTag->setFile($this);
        }

        return $this;
    }

    public function removeFileTag(FileTag $fileTag): self
    {
        if ($this->fileTags->contains($fileTag)) {
            $this->fileTags->removeElement($fileTag);
            // set the owning side to null (unless already changed)
            if ($fileTag->getFile() === $this) {
                $fileTag->setFile(null);
            }
        }

        return $this;
    }
}
