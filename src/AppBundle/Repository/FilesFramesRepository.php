<?php

namespace AppBundle\Repository;

class FilesFramesRepository extends \Doctrine\ORM\EntityRepository
{
    public function getFramesByFileId ($fileId)
    {
        $file = $this->_em->getRepository('AppBundle:Files')->find($fileId);
        return $this->createQueryBuilder('ff')
            ->select(array(
                'ff.id',
                'ff.frame',
                'ff.time',
                'ff.thumb',
                'ff.image'
            ))
            ->where('ff.Files = :file and ff.frame > 0')
            ->setParameter('file', $file)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}