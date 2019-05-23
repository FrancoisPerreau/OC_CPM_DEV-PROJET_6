<?php
// src/CitrespBundle/Form/Security/RegistrationType.php

namespace CitrespBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('city', EntityType::class, [
          'class'        => 'CitrespBundle:City',
          'choice_label' => 'getCityLabel',
          'label' => 'Votre ville'
        ])
        ;
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

}
