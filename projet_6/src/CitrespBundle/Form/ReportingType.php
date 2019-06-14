<?php

namespace CitrespBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ReportingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $textArea = $options['textArea'];

        $builder
          ->add('category', EntityType::class, [
              'class' => 'CitrespBundle:Category',
              'choice_label'=> 'name',
              'label' => 'Sélectionnez une catégorie',
              'required' => true
          ])
          ->add('description', TextareaType::class, [
              'label' => 'Déscription',
              'required' => true,
              'data' => $textArea
          ])
          ->remove('gpsLat')
          ->remove('gpsLng', TextType::class)
          ->remove('dateCreated')
          ->remove('reportedCount')
          ->remove('moderate')
          ->remove('city')
          ->remove('user')
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CitrespBundle\Entity\Reporting',
            'textArea' => ''
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'citrespbundle_reporting';
    }


}
