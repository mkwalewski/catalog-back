<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="files_groups")
 */
class FilesGroups
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false, name="name")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\FilesDisks", mappedBy="FilesGroups")
     */
    private $FilesDisks;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->FilesDisks = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return FilesGroups
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
     * Add filesDisk
     *
     * @param \AppBundle\Entity\FilesDisks $filesDisk
     *
     * @return FilesGroups
     */
    public function addFilesDisk(\AppBundle\Entity\FilesDisks $filesDisk)
    {
        $this->FilesDisks[] = $filesDisk;

        return $this;
    }

    /**
     * Remove filesDisk
     *
     * @param \AppBundle\Entity\FilesDisks $filesDisk
     */
    public function removeFilesDisk(\AppBundle\Entity\FilesDisks $filesDisk)
    {
        $this->FilesDisks->removeElement($filesDisk);
    }

    /**
     * Get filesDisks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilesDisks()
    {
        return $this->FilesDisks;
    }
}
