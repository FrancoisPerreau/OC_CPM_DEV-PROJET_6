<?php
// src/CitrespBundle/Services/CreateAtlContent.php

namespace CitrespBundle\Services;

use CitrespBundle\Entity\Reporting;
use CitrespBundle\Entity\City;

class CreateAtlContent
{
  /**
   * Retourne le contenu de la balise alt des Images pour les Reportings
   * @param  Reporing $reporting
   * @param  City     $city
   * @return [string]
   */
  public function altContent(Reporting $reporting, City $city)
  {
    $alt = $reporting->getCategory()->getName() . ' : ' . $reporting->getAddress() . " - " . $city->getZipcode() . ' ' . $city->getName();

    return $alt;
  }
}
