<?php
// src/CitrespBundle/Services/HydrateReporting.php

namespace CitrespBundle\Services;

use CitrespBundle\Entity\City;
use CitrespBundle\Entity\Reporting;
use CitrespBundle\Entity\User;


class HydrateReporting
{
  private $createAtlContent;

  public function __construct($createAtlContent)
  {
    $this->createAtlContent = $createAtlContent;
  }

  public function hydrate(User $user, City $city, Reporting $reporting)
  {
    $image = $reporting->getImage();
    $alt = $this->createAtlContent->altContent($reporting, $city);

    $reporting->setUser($user);
    $reporting->setCity($city);

    if ($image) {
       $image->setAlt($alt);
    }

  }

}
