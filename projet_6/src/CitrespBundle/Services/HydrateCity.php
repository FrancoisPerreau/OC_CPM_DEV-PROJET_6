<?php
// src/CitrespBundle/Services/HydrateCity.php

namespace CitrespBundle\Services;

use CitrespBundle\Entity\City;


class HydrateCity
{
  public function hydrate(City $city, $formData)
  {
    $city->setName($formData['selectedCity']->getNomCommune());
    $city->setZipcode($formData['selectedCity']->getCodePostal());

    // Coordonnées GPS de la ville pour google map
    $coordinates = explode(', ', $formData['selectedCity']->getCoordonneesGps());
    $cityLat = $coordinates[0];
    $cityLng = $coordinates[1];

    $city->setGpsLat($cityLat);
    $city->setGpsLng($cityLng);
  }
}
