<?php


namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class StudentRepository extends EntityRepository
{
    public function getPaginate($limit = null, $offset = null){
        $qb     = $this->createQueryBuilder('c');

        if (!$limit) {
            return $this->findAll();
        }

        if ($limit < 0 || $offset < 0) {
            return null;
        }

        $qb->setMaxResults($limit);
        if ($offset) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }
}