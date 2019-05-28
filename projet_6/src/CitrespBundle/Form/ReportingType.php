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
        $builder
          ->add('address', TextType::class, [
              'label' => 'N°, nom de la rue',
              'required' => true,
          ])
          ->add('category', EntityType::class, [
              'class' => 'CitrespBundle:Category',
              'choice_label'=> 'name',
              'label' => 'Sélectionnez une catégorie',
              'required' => true
          ])
          ->add('description', TextareaType::class, [
              'label' => 'Déscription',
              'required' => true
          ])
          ->remove('dateCreated')
          ->remove('reportedCount')
          ->remove('moderate')
          ->remove('gpsLat')
          ->remove('gpsLng')
          ->remove('city')
          ->remove('user')
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CitrespBundle\Entity\Reporting'
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
