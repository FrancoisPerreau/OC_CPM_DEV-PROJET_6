<?php
// CitrespBundle/Form/Security/ProfileType.php

namespace CitrespBundle\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;




class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
      $builder
        ->add('notification', CheckboxType::class, [
          'required' => false,
          'label' => 'Notifications'
        ])
        ;
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_profile';
    }

}
