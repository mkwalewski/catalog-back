<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FilesRepository")
 * @ORM\Table(name="files")
 */
class Files
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, name="md5")
     */
    private $md5;

    /**
     * @ORM\Column(type="string", length=300, nullable=false, name="folder")
     */
    private $folder;

    /**
     * @ORM\Column(type="string", length=300, nullable=false, name="filename")
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=10, nullable=false, name="extension")
     */
    private $extension;

    /**
     * @ORM\Column(type="datetime", nullable=false, name="created_time")
     */
    private $createdTime;

    /**
     * @ORM\Column(type="datetime", nullable=false, name="modified_time")
     */
    private $modifiedTime;

    /**
     * @ORM\Column(type="bigint", nullable=false, name="size")
     */
    private $size;

    /**
     * @ORM\Column(type="string", length=20, nullable=true, name="size_formatted")
     */
    private $sizeFormatted;

    /**
     * @ORM\Column(type="string", length=10, nullable=false, name="type")
     */
    private $type;

    /**
     * @ORM\Column(type="integer", nullable=false, name="length")
     */
    private $length;

    /**
     * @ORM\Column(type="string", length=20, nullable=true, name="length_formatted")
     */
    private $lengthFormatted;

    /**
     * @ORM\Column(type="string", length=20, nullable=true, name="video_format")
     */
    private $videoFormat;

    /**
     * @ORM\Column(type="smallint", nullable=true, name="video_width")
     */
    private $videoWidth;

    /**
     * @ORM\Column(type="smallint", nullable=true, name="video_height")
     */
    private $videoHeight;

    /**
     * @ORM\Column(type="decimal", nullable=true, name="video_aspect", precision=6, scale=2)
     */
    private $videoAspect;

    /**
     * @ORM\Column(type="integer", nullable=true, name="video_bitrate")
     */
    private $videoBitrate;

    /**
     * @ORM\Column(type="integer", nullable=true, name="video_fps")
     */
    private $videoFps;

    /**
     * @ORM\Column(type="string", length=20, nullable=true, name="audio_format")
     */
    private $audioFormat;

    /**
     * @ORM\Column(type="string", length=20, nullable=true, name="audio_codec")
     */
    private $audioCodec;

    /**
     * @ORM\Column(type="integer", nullable=true, name="audio_bitrate")
     */
    private $audioBitrate;

    /**
     * @ORM\Column(type="integer", nullable=true, name="audio_rate")
     */
    private $audioRate;

    /**
     * @ORM\Column(type="integer", nullable=true, name="audio_nch")
     */
    private $audioNch;

    /**
     * @ORM\Column(type="smallint", nullable=true, name="image_width")
     */
    private $imageWidth;

    /**
     * @ORM\Column(type="smallint", nullable=true, name="image_height")
     */
    private $imageHeight;

    /**
     * @ORM\Column(type="string", length=20, nullable=true, name="image_mime")
     */
    private $imageMime;

    /**
     * @ORM\Column(type="boolean", nullable=false, name="favorite")
     */
    private $favorite;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FilesFrames", mappedBy="Files")
     */
    private $FilesFrames;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FilesDisks", inversedBy="Files")
     * @ORM\JoinColumn(name="files_disks_id", referencedColumnName="id", nullable=false)
     */
    private $FilesDisks;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->FilesFrames = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set md5
     *
     * @param string $md5
     *
     * @return Files
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;

        return $this;
    }

    /**
     * Get md5
     *
     * @return string
     */
    public function getMd5()
    {
        return $this->md5;
    }

    /**
     * Set folder
     *
     * @param string $folder
     *
     * @return Files
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return Files
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return Files
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set createdTime
     *
     * @param \DateTime $createdTime
     *
     * @return Files
     */
    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;

        return $this;
    }

    /**
     * Get createdTime
     *
     * @return \DateTime
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    /**
     * Set modifiedTime
     *
     * @param \DateTime $modifiedTime
     *
     * @return Files
     */
    public function setModifiedTime($modifiedTime)
    {
        $this->modifiedTime = $modifiedTime;

        return $this;
    }

    /**
     * Get modifiedTime
     *
     * @return \DateTime
     */
    public function getModifiedTime()
    {
        return $this->modifiedTime;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return Files
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set sizeFormatted
     *
     * @param string $sizeFormatted
     *
     * @return Files
     */
    public function setSizeFormatted($sizeFormatted)
    {
        $this->sizeFormatted = $sizeFormatted;

        return $this;
    }

    /**
     * Get sizeFormatted
     *
     * @return string
     */
    public function getSizeFormatted()
    {
        return $this->sizeFormatted;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Files
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set length
     *
     * @param integer $length
     *
     * @return Files
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length
     *
     * @return integer
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set lengthFormatted
     *
     * @param string $lengthFormatted
     *
     * @return Files
     */
    public function setLengthFormatted($lengthFormatted)
    {
        $this->lengthFormatted = $lengthFormatted;

        return $this;
    }

    /**
     * Get lengthFormatted
     *
     * @return string
     */
    public function getLengthFormatted()
    {
        return $this->lengthFormatted;
    }

    /**
     * Set videoFormat
     *
     * @param string $videoFormat
     *
     * @return Files
     */
    public function setVideoFormat($videoFormat)
    {
        $this->videoFormat = $videoFormat;

        return $this;
    }

    /**
     * Get videoFormat
     *
     * @return string
     */
    public function getVideoFormat()
    {
        return $this->videoFormat;
    }

    /**
     * Set videoWidth
     *
     * @param integer $videoWidth
     *
     * @return Files
     */
    public function setVideoWidth($videoWidth)
    {
        $this->videoWidth = $videoWidth;

        return $this;
    }

    /**
     * Get videoWidth
     *
     * @return integer
     */
    public function getVideoWidth()
    {
        return $this->videoWidth;
    }

    /**
     * Set videoHeight
     *
     * @param integer $videoHeight
     *
     * @return Files
     */
    public function setVideoHeight($videoHeight)
    {
        $this->videoHeight = $videoHeight;

        return $this;
    }

    /**
     * Get videoHeight
     *
     * @return integer
     */
    public function getVideoHeight()
    {
        return $this->videoHeight;
    }

    /**
     * Set videoAspect
     *
     * @param string $videoAspect
     *
     * @return Files
     */
    public function setVideoAspect($videoAspect)
    {
        $this->videoAspect = $videoAspect;

        return $this;
    }

    /**
     * Get videoAspect
     *
     * @return string
     */
    public function getVideoAspect()
    {
        return $this->videoAspect;
    }

    /**
     * Set videoBitrate
     *
     * @param integer $videoBitrate
     *
     * @return Files
     */
    public function setVideoBitrate($videoBitrate)
    {
        $this->videoBitrate = $videoBitrate;

        return $this;
    }

    /**
     * Get videoBitrate
     *
     * @return integer
     */
    public function getVideoBitrate()
    {
        return $this->videoBitrate;
    }

    /**
     * Set videoFps
     *
     * @param integer $videoFps
     *
     * @return Files
     */
    public function setVideoFps($videoFps)
    {
        $this->videoFps = $videoFps;

        return $this;
    }

    /**
     * Get videoFps
     *
     * @return integer
     */
    public function getVideoFps()
    {
        return $this->videoFps;
    }

    /**
     * Set audioFormat
     *
     * @param string $audioFormat
     *
     * @return Files
     */
    public function setAudioFormat($audioFormat)
    {
        $this->audioFormat = $audioFormat;

        return $this;
    }

    /**
     * Get audioFormat
     *
     * @return string
     */
    public function getAudioFormat()
    {
        return $this->audioFormat;
    }

    /**
     * Set audioCodec
     *
     * @param string $audioCodec
     *
     * @return Files
     */
    public function setAudioCodec($audioCodec)
    {
        $this->audioCodec = $audioCodec;

        return $this;
    }

    /**
     * Get audioCodec
     *
     * @return string
     */
    public function getAudioCodec()
    {
        return $this->audioCodec;
    }

    /**
     * Set audioBitrate
     *
     * @param integer $audioBitrate
     *
     * @return Files
     */
    public function setAudioBitrate($audioBitrate)
    {
        $this->audioBitrate = $audioBitrate;

        return $this;
    }

    /**
     * Get audioBitrate
     *
     * @return integer
     */
    public function getAudioBitrate()
    {
        return $this->audioBitrate;
    }

    /**
     * Set audioRate
     *
     * @param integer $audioRate
     *
     * @return Files
     */
    public function setAudioRate($audioRate)
    {
        $this->audioRate = $audioRate;

        return $this;
    }

    /**
     * Get audioRate
     *
     * @return integer
     */
    public function getAudioRate()
    {
        return $this->audioRate;
    }

    /**
     * Set audioNch
     *
     * @param integer $audioNch
     *
     * @return Files
     */
    public function setAudioNch($audioNch)
    {
        $this->audioNch = $audioNch;

        return $this;
    }

    /**
     * Get audioNch
     *
     * @return integer
     */
    public function getAudioNch()
    {
        return $this->audioNch;
    }

    /**
     * Set imageWidth
     *
     * @param integer $imageWidth
     *
     * @return Files
     */
    public function setImageWidth($imageWidth)
    {
        $this->imageWidth = $imageWidth;

        return $this;
    }

    /**
     * Get imageWidth
     *
     * @return integer
     */
    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    /**
     * Set imageHeight
     *
     * @param integer $imageHeight
     *
     * @return Files
     */
    public function setImageHeight($imageHeight)
    {
        $this->imageHeight = $imageHeight;

        return $this;
    }

    /**
     * Get imageHeight
     *
     * @return integer
     */
    public function getImageHeight()
    {
        return $this->imageHeight;
    }

    /**
     * Set imageMime
     *
     * @param string $imageMime
     *
     * @return Files
     */
    public function setImageMime($imageMime)
    {
        $this->imageMime = $imageMime;

        return $this;
    }

    /**
     * Get imageMime
     *
     * @return string
     */
    public function getImageMime()
    {
        return $this->imageMime;
    }

    /**
     * Set favorite
     *
     * @param boolean $favorite
     *
     * @return Files
     */
    public function setFavorite($favorite)
    {
        $this->favorite = $favorite;

        return $this;
    }

    /**
     * Get favorite
     *
     * @return boolean
     */
    public function getFavorite()
    {
        return $this->favorite;
    }

    /**
     * Add filesFrame
     *
     * @param \AppBundle\Entity\FilesFrames $filesFrame
     *
     * @return Files
     */
    public function addFilesFrame(\AppBundle\Entity\FilesFrames $filesFrame)
    {
        $this->FilesFrames[] = $filesFrame;

        return $this;
    }

    /**
     * Remove filesFrame
     *
     * @param \AppBundle\Entity\FilesFrames $filesFrame
     */
    public function removeFilesFrame(\AppBundle\Entity\FilesFrames $filesFrame)
    {
        $this->FilesFrames->removeElement($filesFrame);
    }

    /**
     * Get filesFrames
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilesFrames()
    {
        return $this->FilesFrames;
    }

    /**
     * Set filesDisks
     *
     * @param \AppBundle\Entity\FilesDisks $filesDisks
     *
     * @return Files
     */
    public function setFilesDisks(\AppBundle\Entity\FilesDisks $filesDisks)
    {
        $this->FilesDisks = $filesDisks;

        return $this;
    }

    /**
     * Get filesDisks
     *
     * @return \AppBundle\Entity\FilesDisks
     */
    public function getFilesDisks()
    {
        return $this->FilesDisks;
    }
}
