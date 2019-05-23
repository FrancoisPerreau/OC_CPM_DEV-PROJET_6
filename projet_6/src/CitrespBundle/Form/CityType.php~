<?php

namespace CitrespBundle\Form;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class CityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->add('name', EntityType::class, [
              'class' => 'CitrespBundle:City',
              'choice_label' => 'getCityLabel',
              'label' => 'Sélectionnez votre ville',
              'required' => true,
              'query_builder' => function (EntityRepository $er){
                return $er->createQueryBuilder('c')
                  ->orderBy('c.name', 'ASC');
              }

          ])
          ->remove('zipcode')
          ->remove('slug')
          ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CitrespBundle\Entity\City'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'citrespbundle_city';
    }


}
