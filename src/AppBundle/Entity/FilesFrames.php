<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="files_frames")
 */
class FilesFrames
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint", nullable=false, name="frame")
     */
    private $frame;

    /**
     * @ORM\Column(type="time", nullable=true, name="time")
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=300, nullable=false, name="image")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Files", inversedBy="FilesFrames")
     * @ORM\JoinColumn(name="files_id", referencedColumnName="id", nullable=false)
     */
    private $Files;

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
     * Set frame
     *
     * @param integer $frame
     *
     * @return FilesFrames
     */
    public function setFrame($frame)
    {
        $this->frame = $frame;

        return $this;
    }

    /**
     * Get frame
     *
     * @return integer
     */
    public function getFrame()
    {
        return $this->frame;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return FilesFrames
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return FilesFrames
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set files
     *
     * @param \AppBundle\Entity\Files $files
     *
     * @return FilesFrames
     */
    public function setFiles(\AppBundle\Entity\Files $files)
    {
        $this->Files = $files;

        return $this;
    }

    /**
     * Get files
     *
     * @return \AppBundle\Entity\Files
     */
    public function getFiles()
    {
        return $this->Files;
    }
}
