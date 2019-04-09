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

    public function getTreeFilesByDiskId ($catalogDiskId)
    {
        $filesDisks = $this->_em->getRepository('AppBundle:FilesDisks')->find($catalogDiskId);
        return $this->createQueryBuilder('f')
            ->select(array(
                'f.id',
                'f.type',
                'f.folder',
                'f.filename',
                'f.extension',
                'f.size',
                'f.sizeFormatted',
                'f.length',
                'f.lengthFormatted',
                'f.videoWidth',
                'f.videoHeight',
                'f.favorite',
                'ff.image'
            ))
            ->leftJoin('AppBundle:FilesFrames', 'ff', 'WITH', 'ff.Files = f.id AND ff.frame = 0')
            ->where('f.FilesDisks = :filesDisks')
            ->setParameter('filesDisks', $filesDisks)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}