<?php

namespace CitrespBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
// use Symfony\Component\Form\Extension\Core\Type\ImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegisterReportingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('autocompleteInput', TextType::class, [
              'label' => 'N°, nom de la rue',
              'required' => true,
            //   'mapped' => false
          ])
          ->add('image', ImageType::class, [
              'required' => false,
              'label' => false
          ])
          ->add('address', HiddenType::class)
          ->add('gpsLat', HiddenType::class)
          ->add('gpsLng', HiddenType::class)
        ;
    }

    public function getParent()
    {
        return ReportingType::class;
    }
    
}
