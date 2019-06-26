<?php

namespace CitrespBundle\Repository;

/**
 * reportingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class reportingRepository extends \Doctrine\ORM\EntityRepository
{
  public function getReportingByReported($city)
  {
    $qb = $this->createQueryBuilder('r');

    $qb
      ->select('r')
      ->where('r.city = :city')
      ->setParameter('city', $city)
      ->andWhere('r.reportedCount > 0')
      ->orderBy('r.dateCreated','DESC')
    ;

    return $qb
      ->getQuery()
      ->getResult()
    ;
  }


  public function getReportingByReportedNb($city)
  {
    $qb = $this->createQueryBuilder('r');

    $qb
      ->select('COUNT(r)')
      ->where('r.city = :city')
      ->setParameter('city', $city)
      ->andWhere('r.reportedCount > 0')
    ;

    return $qb
      ->getQuery()
      // ->getResult()
      ->getSingleScalarResult();
    ;
  }

}
