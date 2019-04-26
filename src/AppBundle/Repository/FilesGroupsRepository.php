<?php

namespace AppBundle\Repository;

class FilesGroupsRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAll ()
    {
        return $this->createQueryBuilder('g')
            ->select(array(
                'g.id',
                'g.name',
            ))
            ->orderBy('g.name', 'ASC')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}