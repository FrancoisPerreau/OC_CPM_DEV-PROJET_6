<?php

namespace CitrespBundle\Repository;



class BaseCitiesRepository extends \Doctrine\ORM\EntityRepository
{
  public function getCitiesBaseByCodePostal($codePostal)
  {
    $qb = $this->createQueryBuilder('c');

    $qb
      ->where('c.codePostal = :codePostal')
      ->setParameter('codePostal', $codePostal)
      ->orderBy('c.nomCommune', 'ASC')
    ;

    return $qb
      ->getQuery()
      ->getResult()
    ;
  }
}
