<?php
// src/CitrespBundle/Services/CitiesNotCreated.php

namespace CitrespBundle\Services;

use CitrespBundle\Entity\BaseCities;
use CitrespBundle\Entity\City;

class CitiesNotCreated
{
  public function resultCheck (array $selectedBaseCities, array $checkCities)
  {
    $checkCitiesName = [];
    $resultCheck = [];

    // On rempli 1 array avec les noms des villes déjà créées avec ce code postal
    foreach ($checkCities as $checkCity)
    {
        $checkCitiesName[] = strtoupper($checkCity->getName());
    }

    // On rempli un array avec les villes (BaseCities) qui ont ce code postal mais qui n'existent pas encore
    foreach ($selectedBaseCities as $selectedBaseCity)
    {
        if (!in_array(strtoupper($selectedBaseCity->getNomCommune()), $checkCitiesName))
        {
            $resultCheck[] = $selectedBaseCity;
        }
    }

    return $resultCheck;
  }

}
