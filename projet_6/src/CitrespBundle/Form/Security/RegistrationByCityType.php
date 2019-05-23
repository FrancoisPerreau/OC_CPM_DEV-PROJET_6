<?php
// src/CitrespBundle/Form/Security/RegistrationByCityType.php

namespace CitrespBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class RegistrationByCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
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
