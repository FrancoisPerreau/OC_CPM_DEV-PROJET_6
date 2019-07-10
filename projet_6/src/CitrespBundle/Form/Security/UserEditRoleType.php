<?php
// src/CitrespBundle/Form/Security/UserEditRoleType.php

namespace CitrespBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserEditRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('roles', ChoiceType::class, [
          'choices' => [
              'Administrateur' =>'ROLE_ADMIN',
              'Modérateur' =>'ROLE_MODERATOR',
              'Ville' =>'ROLE_CITY',
              'Utilisateur' =>'ROLE_USER'

            // 'ROLE_ADMIN' => 'Administrateur',
            // 'ROLE_MODERATOR' => 'Modérateur',
            // 'ROLE_CITY' => 'Ville',
            // 'ROLE_USER' => 'Utilisateur'
          ],
          'multiple' => true,
          'required' => true,
          'label' => 'Rôle'
        ])
        ->remove('email')
        ->remove('username')
        ->remove('plainPassword')
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
