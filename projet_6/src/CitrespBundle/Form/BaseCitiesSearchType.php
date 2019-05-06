<?php
// src/CitrespBundle/Form/BaseCitiesSearchType.php

namespace CitrespBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class BaseCitiesSearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('searchedCity', TextType::class, [
            'label' => 'Code postal de votre ville',
            'required' => true,
          ]);
    }

}
