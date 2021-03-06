<?php
// CitrespBundle/Form/Security/RegistrationType.php

namespace CitrespBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;




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
        ->add('notification', CheckboxType::class, [
          'required' => false,
          'label' => 'Notifications'
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
