<?php
// src/CitrespBundle/Services/AddOneToReportedCount.php

namespace CitrespBundle\Services;

use Symfony\Component\Config\Definition\Exception\Exception;

use CitrespBundle\Entity\Reporting;
use CitrespBundle\Entity\Comment;


class AddOneToReportedCount
{
  private $addOne;

  public function __construct($addOne)
  {
    $this->addOne = $addOne;
  }


  public function add($object)
  {
    try {
      if ($object instanceof Reporting || $object instanceof Comment)
      {
        $reportedCount = $object->getReportedCount();
        $reportedCount = $this->addOne->add($reportedCount);

        $object->setReportedCount($reportedCount);
      }

    } catch (\Exception $e) {
      throw new Exception("Une erreur est survenue.");
    }

  }

}
