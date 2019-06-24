<?php
// src/CitrespBundle/Services/CheckUserCity.php

namespace CitrespBundle\Services;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;


use CitrespBundle\Entity\City;
use CitrespBundle\Entity\User;

class CheckUserCity
{
  private $session;
  private $router;


  public function __construct(Session $session, RouterInterface $router)
  {
     $this->session = $session;
     $this->router = $router;
  }

  public function check(User $user, City $city)
  {


    if ($user->getCity() != $city)
    {
        $this->session->getFlashBag()->add('errorCityAccess', 'Votre compte n\'est pas pour la ville de ' . $city->getName());

        return $this->router->redirectToRoute('homepage');
    }
  }
}
