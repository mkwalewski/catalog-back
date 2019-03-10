<?php

namespace AppBundle\Service;

use AppBundle\Entity\Files;
use AppBundle\Entity\FilesDisks;

class Catlog
{
    const excludedFolders = ['$RECYCLE.BIN','System Volume Information'];

    public function __construct ($em, $dir)
    {
        $this->em = $em;
        $this->dir = $dir;
    }

    public function getPartitions ()
    {
        $disks = [];
        $output = shell_exec('wmic logicaldisk get name');

        if ($output)
        {
            foreach (explode ("\r\n", $output) as $item)
            {
                if (preg_match('#([A-Z]{1}\:)#ui', $item, $matches))
                {
                    $disks[] = $matches[1];
                }
            }
        }

        return $disks;
    }

    public function getFolders ($path)
    {
        $folders = [];

        if ($path && is_dir($path))
        {
            foreach (scandir($path) as $file)
            {
                if ($file == '.' || $file == '..' || in_array($file, self::excludedFolders))
                {
                    continue;
                }
                $fullPath = $path . DIRECTORY_SEPARATOR . $file;
                if (is_dir ($fullPath))
                {
                    $folders[] = $file;
                }
            }
        }

        return $folders;
    }

    public function getFiles ($path)
    {
        $files = [];

        if ($path && is_dir($path))
        {
            foreach (scandir($path) as $file)
            {
                if ($file == '.' || $file == '..')
                {
                    continue;
                }
                $fullPath = $path . DIRECTORY_SEPARATOR . $file;
                if (is_file ($fullPath))
                {
                    $files[] = $fullPath;
                }
            }
        }

        return $files;
    }

    public function addCatalog ($name, $path)
    {
        $id = 0;
        $files = [];

        if ($name && $path)
        {
            $disk = new FilesDisks();
            $disk->setType(1);
            $disk->setName($name);
            $disk->setPath($path);
            $this->em->persist($disk);
            $this->em->flush();
            $id = $disk->getId();
            $files = $this->getFiles($path);
        }

        $data = [
            'id' => $id,
            'files' => $files
        ];

        return $data;
    }

    public function addCatalogFile ($catalogDiskId, $path)
    {
        //@TODO
    }
}