<?php

namespace AppBundle\Repository;

class FilesRepository extends \Doctrine\ORM\EntityRepository
{
    public function getTreeFolders ($catalogDiskId)
    {
        $filesDisks = $this->_em->getRepository('AppBundle:FilesDisks')->find($catalogDiskId);
        return $this->createQueryBuilder('f')
            ->select('DISTINCT f.folder')
            ->where('f.FilesDisks = :filesDisks')
            ->setParameter('filesDisks', $filesDisks)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}