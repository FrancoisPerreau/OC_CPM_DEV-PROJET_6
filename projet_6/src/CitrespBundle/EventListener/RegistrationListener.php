<?php
// src/CitrespBundle/EventListener/RegistrationListener

namespace CitrespBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegistrationListener implements EventSubscriberInterface
{
  public static function getSubscribedEvents()
  {
    return [
      FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess'
    ];
  }


  public function onRegistrationSuccess(FormEvent $event)
  {
    $role = 'ROLE_USER';

    $user = $event->getForm()->getData();

    $user->setRoles(['ROLE_USER']);

      dump($user);
      die;
  }
}
