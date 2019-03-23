<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="files_disks")
 */
class FilesDisks
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=300, nullable=false, name="path")
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=100, nullable=false, name="name")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Files", mappedBy="FilesDisks")
     */
    private $Files;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FilesGroups", inversedBy="FilesDisks")
     * @ORM\JoinColumn(name="files_groups_id", referencedColumnName="id", nullable=false)
     */
    private $FilesGroups;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Files = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set path
     *
     * @param string $path
     *
     * @return FilesDisks
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return FilesDisks
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add file
     *
     * @param \AppBundle\Entity\Files $file
     *
     * @return FilesDisks
     */
    public function addFile(\AppBundle\Entity\Files $file)
    {
        $this->Files[] = $file;

        return $this;
    }

    /**
     * Remove file
     *
     * @param \AppBundle\Entity\Files $file
     */
    public function removeFile(\AppBundle\Entity\Files $file)
    {
        $this->Files->removeElement($file);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->Files;
    }

    /**
     * Set filesGroups
     *
     * @param \AppBundle\Entity\FilesGroups $filesGroups
     *
     * @return FilesDisks
     */
    public function setFilesGroups(\AppBundle\Entity\FilesGroups $filesGroups)
    {
        $this->FilesGroups = $filesGroups;

        return $this;
    }

    /**
     * Get filesGroups
     *
     * @return \AppBundle\Entity\FilesGroups
     */
    public function getFilesGroups()
    {
        return $this->FilesGroups;
    }
}
