<?php
// src/CitrespBundle/Form/loginFormType.php

namespace Citresp\Form\Security;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;



class LoginFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('_username', TextType::class)
      ->add('_password', PasswordType::class);
  }
}
