<?php
// src/CitrespBundle/Services/HydrateReporting.php

namespace CitrespBundle\Services;

use CitrespBundle\Entity\City;
use CitrespBundle\Entity\Reporting;
use CitrespBundle\Entity\User;
use CitrespBundle\Entity\Status;

class HydrateReporting
{
  private $createAtlContent;

  public function __construct($createAtlContent)
  {
    $this->createAtlContent = $createAtlContent;
  }

  public function hydrate(User $user, City $city, Reporting $reporting, Status $status, $address, $gpsLat, $gpsLng)
  {
    $image = $reporting->getImage();
    $alt = $this->createAtlContent->altContent($reporting, $city);

    $reporting->setUser($user);
    $reporting->setCity($city);
    $reporting->setStatus($status);
    $reporting->setAddress($address);
    $reporting->setGpsLat($gpsLat);
    $reporting->setGpsLng($gpsLng);

    if ($image) {
       $image->setAlt($alt);
    }

  }

}
