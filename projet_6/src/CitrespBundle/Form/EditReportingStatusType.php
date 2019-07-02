<?php
// src/CitrespBundle/Form/EditReportingStatusType.php

namespace CitrespBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class EditReportingStatusType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('status', EntityType::class, [
              'class' => 'CitrespBundle:Status',
              'choice_label' => 'name',
              'label' => 'Sélectionnez un statut',
              'required' => true,
              'query_builder' => function (EntityRepository $er){
                return $er->createQueryBuilder('s')
                  ->orderBy('s.name', 'ASC');
              }
          ])
          ->remove('category')
          ->remove('description')
        ;
    }

    public function getParent()
    {
        return ReportingType::class;
    }

    // /**
    //  * {@inheritdoc}
    //  */
    // public function getBlockPrefix()
    // {
    //     return 'citrespbundle_reporting';
    // }


}
