<?php
// src/CitrespBundle/Form/BaseCitiesChoiceType.php

namespace CitrespBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Validator\Constraints\NotBlank;


class BaseCitiesChoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $selectedBaseCities = $options['allow_extra_fields'];
        
        $builder
          ->add('selectedCity', ChoiceType::class, [
              'choices' => $selectedBaseCities,
              'choice_label' => 'nomCommune',
              'required' => true,
              'label' => 'Sélectionnez votre ville',
              'constraints' => new NotBlank()
          ]);
    }

}
