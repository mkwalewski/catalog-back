<?php

namespace AppBundle\Repository;

class FilesDisksRepository extends \Doctrine\ORM\EntityRepository
{
    public function getDisksByGroupId ($groupId)
    {
        $group = $this->_em->getRepository('AppBundle:FilesGroups')->find($groupId);
        return $this->createQueryBuilder('d')
            ->select(array(
                'd.id',
                'd.path',
                'd.name',
            ))
            ->where('d.FilesGroups = :group')
            ->setParameter('group', $group)
            ->orderBy('d.name', 'ASC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}