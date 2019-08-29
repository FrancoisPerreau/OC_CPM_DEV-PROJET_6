<?php
// src/CitrespBundle/Services/CheckUserCity.php

namespace CitrespBundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;


use CitrespBundle\Entity\City;
use CitrespBundle\Entity\User;

class CheckUserCity
{
  

  public function checkIsCity(User $user, City $city)
  {
    $checkResult = [];
    
    if ($user->getCity() != $city)
    {
      $checkResult['isCity'] = false;
      $checkResult['message'] = 'Votre compte n\'est pas pour la ville de ' . $city->getName();              
    }
    else 
    {
      $checkResult['isCity'] = true;
    }

    return $checkResult;
  }

}
