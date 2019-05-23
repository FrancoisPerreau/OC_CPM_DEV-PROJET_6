<?php

namespace CitrespBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class BaseCitiesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nomCommune', EntityType::class, [
            'class' => 'CitrespBundle:BaseCities',
            'choice_label' => 'getCityLabel',
            'required' => true,
            // 'label' => 'Sélectionnez votre ville',
            // 'query_builder' => function (EntityRepository $er){
            //   return $er->createQueryBuilder('c')
            //     ->orderBy('c.nomCommune', 'ASC');
            // }
        ])
          // ->add('nomCommune')
          // ->add('codePostal')
          // ->add('libelleAcheminement')
          // ->add('coordonneesGps')
          ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CitrespBundle\Entity\BaseCities'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'citrespbundle_basecities';
    }


}
